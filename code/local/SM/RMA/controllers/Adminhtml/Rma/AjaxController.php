<?php

class SM_RMA_Adminhtml_Rma_AjaxController extends SM_Barcode_Controller_Adminhtml_Action {

    public function loadReturnBarcodeImageAction() {
        $result = array();

//        $product_id = (int) substr($this->getRequest()->getPost('order_product_id'), 0, -1);
//        if ($this->getRequest()->getPost('scanned'))
//            foreach ($this->getRequest()->getPost('scanned') as $k => $v) {
//                $product_id = (int) $k;
//            }
//        $result['product_id'] = $product_id;
//        $product = Mage::getModel('catalog/product')->load($product_id);

        if ($this->getRequest()->getPost('scanned')) {
            foreach ($this->getRequest()->getPost('scanned') as $k => $v) {
                $product_id = (int) $k;
                $product = Mage::getModel('catalog/product')->load($product_id);
            }
        } else {
            if (intval(Mage::getStoreConfig("barcode/product/conversion") == 1)):
                switch (intval(Mage::getStoreConfig('barcode/product/barcode_field'))) {
                    case 0:
                        $product_id = intval(substr($this->getRequest()->getPost('order_product_id'), 0, -1));
                        $result['product_id'] = $product_id;
                        $product = Mage::getModel('catalog/product')->load($product_id);
                        break;
                    case 1:
                        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $sku = substr(trim($this->getRequest()->getPost('order_product_id')), 0, -1);
                        $readresult = $write->query("SELECT `e`.* FROM `catalog_product_entity` AS `e` WHERE (SUBSTRING(CONV(SUBSTRING(CAST(MD5(`e`.`sku`) AS CHAR),1,16),16,10),1,12) = '" . $sku . "')");
                        $row = $readresult->fetch();
                        $product_id = $row['entity_id'];
                        //$product = Mage::getModel('catalog/product')->loadByAttribute('sku', substr(trim($this->getRequest()->getPost('order_product_id')), 0, -1));
                        $product = Mage::getModel('catalog/product')->load($product_id);
                        $product_id = $product->getId();
                        $result['product_id'] = $product_id;
                        break;
                }
            else:

                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $attr = Mage::getStoreConfig("barcode/product/barcode_value");
                $attr_val = $this->getRequest()->getPost('order_product_id');
                if ($attr != "98") { //SKU
                    $readresult = $write->query("SELECT `e`.*, `at_name`.`value` AS `name` FROM `catalog_product_entity` AS `e` INNER JOIN `catalog_product_entity_varchar` AS `at_name` ON (`at_name`.`entity_id` = `e`.`entity_id`) AND (`at_name`.`attribute_id` = '" . $attr . "') AND (`at_name`.`store_id` = 0) WHERE at_name.value = '" . $attr_val . "'");
                    $row = $readresult->fetch();
                    $product_id = $row['entity_id'];
                    $product = Mage::getModel('catalog/product')->load($product_id);
                } else {
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', trim($this->getRequest()->getPost('order_product_id')));
                }
                $product_id = $product->getId();
                $result['product_id'] = $product_id;
            endif;
        }
        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));

        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = Mage::getModel('sales/order_item')->getCollection()
                ->addFieldToSelect('product_id')
                ->addFieldToFilter('order_id', $order->getId())
                ->addFieldToFilter('product_type', 'simple')
        //->addFieldToFilter('qty_shipped',array('gt' => 0))
        ;
        $product_qty = array();
        $product_ids = array();
        foreach ($items as $item) {
            $product_qty[$item->getProductId()] = $item->getQtyOrdered();
            $product_ids[] = (int) $item->getProductId();
        }
        $result['product_ids'] = $product_ids;

        if (!$product || !$product->getId()) {
            $result['error'] = Mage::helper('barcode')->__('Product ID is not valid.');
        } else {
            //gen barcode
            Mage::helper('barcode/barcode')->createProductBarcode($product_id);
            $result['product_id'] = $product_id;

            //check product in items list
            if (in_array($product_id, $product_ids)) {
                $result['product_in_order'] = 1;
            } else {
                $result['product_in_order'] = 0;
            }
            $result['image_name'] = $product_id;
            $result['error'] = '';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function saveAction() {
        $post = $this->getRequest()->getPost();

        $result = array();
        $action = $this->getRequest()->getPost('save_action');

        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));

        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);

        $order = Mage::getModel('sales/order')->loadByIncrementId((int) $order_id);
        if (!$order || !$order->getId()) {
            $result['message'] = 'Order #' . $order_id . ' not found';
            $result['error'] = true;
        }

        if ($action == 'create_rma') {
            $data = array();
            $data['store_id'] = $order->getStoreId();
            $data['order_id'] = $order->getId();
            $data['order_increment_id'] = $order->getIncrementId();
            $data['customer_id'] = $order->getCustomerIsGuest() ? 0 : $order->getCustomerId();
            $data['customer_name'] = $order->getCustomerIsGuest() ? 'Guest' : $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            $data['customer_email'] = $order->getCustomerEmail();
            $data['package_opened'] = 1;
            $data['request_type'] = 2;
            $data['status'] = SM_RMA_Model_Request::STATUS_APPROVED;
            $data['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $data['created_by'] = 'admin';
            $request = Mage::getModel('rma/request')->setId(null);
            try {
                $request->setData($data);
                $request->save();

                //save request items
                $item = Mage::getModel('rma/item')->setId(null);
                foreach ($post['items'] as $key => $value) {
                    if ($value > 0) {
                        $data = array();
                        $data['rma_id'] = $request->getId();
                        $data['item_id'] = $key;
                        $data['qty_to_return'] = intval($value);
                        try {
                            $item->setData($data);
                            $item->save();
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    }
                }

                //add comment for RMA
                $msg_data = array();
                $msg_data['rma_id'] = $request->getId();
                $msg_data['customer_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
                $msg_data['customer_name'] = Mage::getSingleton('admin/session')->getUser()->getName();
                $msg_data['customer_email'] = Mage::getSingleton('admin/session')->getUser()->getEmail();
                $msg_data['content'] = $this->__('Your request has been approved successful.');
                $msg_data['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                $comment = Mage::getModel('rma/comment');
                try {
                    $comment->setData($msg_data);
                    $comment->setId(null)->save();

                    $result['message'] = 'Created new RMA request for order #' . $order_id . ' successful.';
                    $result['error'] = false;
                } catch (Exception $e) {
                    $result['message'] = $e->getMessage();
                    $result['error'] = true;
                }
            } catch (Exception $e) {
                $result['message'] = 'Fatal Error: ' . $e->getMessage();
                $result['error'] = true;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function checkReturnOrderAction() {
        $result = array();
        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));
        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if (!$order || !$order->getId()) {
            $result['message'] = 'Order #' . $order_id . ' not found';
            $result['error'] = true;
        } else {
            $shipments = $order->getShipmentsCollection();
            if (count($shipments->getData()) === 0) {
                $result['message'] = 'Order #' . $order_id . ' hasn\'t any shipments.';
                $result['error'] = true;
            } else {
                $result['error'] = false;
                $result['order_id'] = $order_id;
                $items = Mage::getModel('sales/order_item')->getCollection()
                        ->addFieldToSelect('product_id')
                        ->addFieldToFilter('order_id', $order->getId())
                        ->addFieldToFilter('product_type', 'simple')
                //->addFieldToFilter('qty_shipped',array('gt' => 0))
                ;
                $product_ids = array();
                foreach ($items as $item) {
                    $product_qty[$item->getProductId()] = $item->getQtyOrdered();
                    $product_ids[] = (int) $item->getProductId();
                }
                $result['product_ids'] = $product_ids;
            }
        }

        // check valid duration
        $result['valid_duration'] = true;

        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                ->setOrderFilter($order)
                ->load();
        $latestShipmentDate = null;
        foreach ($shipmentCollection as $shipment) {
            $tmpDate = strtotime($shipment->getUpdatedAt());
            if ($latestShipmentDate == null)
                $latestShipmentDate = $tmpDate;
            if ($tmpDate > $latestShipmentDate)
                $latestShipmentDate = $tmpDate;
        }
        $validDuration = Mage::getStoreConfig('barcode/rma/valid_duration'); //get valid duration from config
        $validDuration = $validDuration * 24 * 60 * 60; // convert to seconds
        if ($latestShipmentDate != null) {
            $latestShipmentDate = (date('m/d/y h:i:s', Mage::getModel('core/date')->timestamp($latestShipmentDate)));
            $validDate = date('m/d/y h:i:s', Mage::getModel('core/date')->timestamp(time() - $validDuration));
            if ($latestShipmentDate < $validDate) {
                // order is valid for RMA => add to array+
                $result['valid_duration'] = false;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function createShipment($orderId, $data) {
        if ($shipment = $this->_initShipment($orderId, $data)) {
            $shipment->register();
            $shipment->setEmailSent(false);
            $shipment->getOrder()->setCustomerNoteNotify(false);
            $this->_saveShipment($shipment);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Save shipment and order in one transaction
     * @param Mage_Sales_Model_Order_Shipment $shipment
     */
    protected function _saveShipment($shipment) {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();

        return $this;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _initShipment($orderId, $data) {
        $shipment = false;
        $order = Mage::getModel('sales/order')->load($orderId);

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            return false;
        }
        /**
         * Check shipment is available to create separate from invoice
         */
        if ($order->getForcedDoShipmentWithInvoice()) {
            return false;
        }
        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
            return false;
        }
        $savedQtys = $this->_getItemQtys($data);
        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);

        return $shipment;
    }

    public function submitCommentAction() {
        $result = array();

        if ($data = $this->getRequest()->getPost()) {
            $rma_id = intval($data['id']);
            $request = Mage::getModel('rma/request')->load($rma_id);

            $msg_data = array();
            $msg_data['rma_id'] = $request->getId();
            $msg_data['customer_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
            $msg_data['customer_name'] = Mage::getSingleton('admin/session')->getUser()->getName();
            $msg_data['customer_email'] = Mage::getSingleton('admin/session')->getUser()->getEmail();
            $msg_data['content'] = $data['comment'];
            $msg_data['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $comment = Mage::getModel('rma/comment');
            try {
                $comment->setData($msg_data);
                $comment->setId(null)->save();

                $result['error'] = false;
                $result['content'] = $data['comment'];
                $result['customer_name'] = $msg_data['customer_name'];
                $result['created_time'] = Mage::helper('core')->formatDate($msg_data['created_time'], 'medium', true);
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
        } else {
            $result['error'] = 'UNKNOW ERROR';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Initialize shipment items QTY
     */
    protected function _getItemQtys($data) {
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

}
