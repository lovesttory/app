<?php

/**
 * SmartOSC Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category   SM
 * @package    SM_Barcode
 * @version    2.0
 * @author     hoadx@smartosc.com
 * @copyright  Copyright (c) 2010-2011 SmartOSC Co. (http://www.smartosc.com)
 */
class SM_Barcode_Adminhtml_Barcode_AjaxController extends SM_Barcode_Controller_Adminhtml_Action {

    public function loadBarcodeImageAction() {
        $result = array();
        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $product_id = intval(substr($this->getRequest()->getPost('order_product_id'), 0, -1));
        else
            $product_id = intval($this->getRequest()->getPost('order_product_id'));        
        if (intval(Mage::getStoreConfig("barcode/product/conversion") == 1)):
            switch (intval(Mage::getStoreConfig('barcode/product/barcode_field'))) {
                case 0:
                    $result['product_id'] = $product_id;
                    $product = Mage::getModel('catalog/product')->load($product_id);
                    break;
                case 1:
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $sku = $product_id;
                    $readresult = $write->query("SELECT `e`.* FROM `catalog_product_entity` AS `e` WHERE (SUBSTRING(CONV(SUBSTRING(CAST(MD5(`e`.`sku`) AS CHAR),1,16),16,10),1,12) = '" . $sku . "')");
                    $row = $readresult->fetch();
                    $product_id = $row['entity_id'];
                    //$product = Mage::getModel('catalog/product')->loadByAttribute('sku', substr(trim($this->getRequest()->getPost('order_product_id')), 0, -1));
                    $product = Mage::getModel('catalog/product')->load($product_id);
                    $product_id = $product->getId();
                    $result['product_id'] = $product_id;
                    break;
                case 2:
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $attr = Mage::getStoreConfig("barcode/product/barcode_source");
                    $attr_val = $product_id;

                    $readresult = $write->query("SELECT `e`.*, `at_name`.`value` AS `name` FROM `catalog_product_entity` AS `e` INNER JOIN `catalog_product_entity_varchar` AS `at_name` ON (`at_name`.`entity_id` = `e`.`entity_id`) AND (`at_name`.`attribute_id` = '" . $attr . "') AND (`at_name`.`store_id` = 0) WHERE ((SUBSTRING(CONV(SUBSTRING(CAST(MD5(at_name.value) AS CHAR),1,16),16,10),1,12) = '" . $attr_val . "'))");
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
            if ($attr != "sku" && $attr != 98) {
                $readresult = $write->query("SELECT `e`.*, `at_name`.`value` AS `name` FROM `catalog_product_entity` AS `e` INNER JOIN `catalog_product_entity_varchar` AS `at_name` ON (`at_name`.`entity_id` = `e`.`entity_id`) AND (`at_name`.`attribute_id` = '" . $attr . "') AND (`at_name`.`store_id` = 0) WHERE at_name.value = '" . $attr_val . "'");
                //var_dump("SELECT `e`.*, `at_name`.`value` AS `name` FROM `catalog_product_entity` AS `e` INNER JOIN `catalog_product_entity_varchar` AS `at_name` ON (`at_name`.`entity_id` = `e`.`entity_id`) AND (`at_name`.`attribute_id` = '" . $attr . "') AND (`at_name`.`store_id` = 0) WHERE ((SUBSTRING(CONV(SUBSTRING(CAST(MD5(at_name.value) AS CHAR),1,16),16,10),1,12) = '" . $attr_val . "'))");die;
                $row = $readresult->fetch();
                $product_id = $row['entity_id'];
                $product = Mage::getModel('catalog/product')->load($product_id);
            } else {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', trim($this->getRequest()->getPost('order_product_id')));
            }
            if ($product && $product->getId())
                $product_id = $product->getId();
            $result['product_id'] = $product_id;
        endif;

        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));

        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = Mage::getModel('sales/order_item')->getCollection()
                //->addFieldToSelect('product_id')
                ->addFieldToFilter('order_id', $order->getId())
                ->addFieldToFilter('product_type', 'simple');
        $product_qty = array();
        $product_ids = array();
        foreach ($items as $item) {
            $product_qty[$item->getProductId()] = $item->getQtyOrdered();
            $product_ids[] = (int) $item->getProductId();
        }
        $result['product_ids'] = $product_ids;

        if (!$product || $product->getName() == '') {
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

        $this->getResponse()->setBody(Mage::helper('barcode')->jsonEncode($result));
    }

    public function saveAction() {
        $result = array();
        $action = $this->getRequest()->getPost('save_action');
        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));
        //$order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

        if (!$order || !$order->getId()) {
            $result['message'] = 'Order #' . $order_id . ' not found';
            $result['error'] = true;
        }

        if ($action == 'complete') {
            try {
                //create shipment
                $data = $this->getRequest()->getPost('shipment');
                if (!$this->createShipment($order, $data)) {
                    die('cannot create shipment');
                }

                //////////////////////////////////////////////
                // Fix error cannot change protected state - zzz
                //$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, Mage_Sales_Model_Order::STATE_COMPLETE);
                $order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
                $status = $order->getConfig()->getStateDefaultStatus(Mage_Sales_Model_Order::STATE_COMPLETE);
                $order->setStatus($status);
                ///////////////////////////////////////////////

                $order->save();
                $result['message'] = 'Order #' . $order_id . ' has been changed to completed.';
                $result['error'] = false;
            } catch (Exception $e) {
                $result['message'] = 'Fatal Error: ' . $e->getMessage();
                $result['error'] = true;
            }
        } elseif ($action == 'hold') {
            try {
                if ($order->canHold()) {
                    $order->hold()->save();
                    $result['message'] = 'Order #' . $order_id . ' has been put on hold';
                    $result['error'] = false;
                } else {
                    $result['message'] = 'Order #' . $order_id . ' was not put on hold';
                    $result['error'] = true;
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
            }
        } elseif ($action == 'backorder') {
            try {
                $result['message'] = 'This function is in processing status';
                $result['error'] = true;
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['error'] = true;
            }
        } elseif ($action == 'partial') {
            try {
                //create shipment
                $data = $this->getRequest()->getPost('shipment');
                if (!$this->createShipment($order, $data)) {
                    die('cannot create shipment');
                }
                $result['message'] = 'The shipment of this order has been created';
                $result['error'] = false;
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['error'] = true;
            }
        }


        $this->getResponse()->setBody(Mage::helper('barcode')->jsonEncode($result));
    }

    public function checkOrderAction() {
        $result = array();
        if (intval(Mage::getStoreConfig("barcode/general/symbology")) == 0)
            $order_id = intval(substr($this->getRequest()->getPost('order_id'), 0, -1));
        else
            $order_id = intval($this->getRequest()->getPost('order_id'));
        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);


        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if (!$order || !$order->getId()) {
            $result['message'] = $this->__('Order not found for barcode #' . $this->getRequest()->getPost('order_id'));
            $result['error'] = true;
        } elseif ($order->getStatus() != Mage_Sales_Model_Order::STATE_PROCESSING) {
            $result['message'] = $this->__('Order #' . $order_id . ' has status Complete, or invoice is not created');
            $result['error'] = true;
        } else {
            if ($order->getStatus() == Mage_Sales_Model_Order::STATE_COMPLETE) {
                $result['message'] = $this->__('Order #' . $order_id . ' was completed');
                $result['error'] = true;
            } else {
                $result['error'] = false;
                $result['order_id'] = $order_id;
                $items = Mage::getModel('sales/order_item')->getCollection()
                        //->addFieldToSelect('product_id')  // fix compatible with 1.3
                        ->addFieldToFilter('order_id', $order->getId())
                        ->addFieldToFilter('product_type', 'simple');
                $product_ids = array();
                foreach ($items as $item) {
                    $product_qty[$item->getProductId()] = $item->getQtyOrdered();
                    $product_ids[] = (int) $item->getProductId();
                }
                $result['product_ids'] = $product_ids;
            }
        }

        $this->getResponse()->setBody(Mage::helper('barcode')->jsonEncode($result));
    }

    public function checkReturnOrderAction() {
        $result = array();
        $order_id = intval($this->getRequest()->getPost('order_id'));
        if (strlen($order_id) > 9)
            $order_id = substr($order_id, 0, -1) . "-" . substr($order_id, strlen($order_id) - 1, 1);
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if (!$order || !$order->getId()) {
            $result['message'] = $this->__('Order #' . $order_id . ' not found');
            $result['error'] = true;
        } else {
            $shipments = $order->getShipmentsCollection();
            if (count($shipments->getData()) === 0) {
                $result['message'] = $this->__('Order #' . $order_id . ' hasn\'t any shipments.');
                $result['error'] = true;
            } else {
                $result['error'] = false;
                $result['order_id'] = $order_id;
                $items = Mage::getModel('sales/order_item')->getCollection()
                        //->addFieldToSelect('product_id')
                        ->addFieldToFilter('order_id', $order->getId())
                        ->addFieldToFilter('product_type', 'simple')
                        ->addFieldToFilter('qty_shipped', array('gt' => 0));
                $product_ids = array();
                foreach ($items as $item) {
                    $product_qty[$item->getProductId()] = $item->getQtyOrdered();
                    $product_ids[] = (int) $item->getProductId();
                }
                $result['product_ids'] = $product_ids;
            }
        }

        $this->getResponse()->setBody(Mage::helper('barcode')->jsonEncode($result));
    }

    protected function createShipment($order, $data) {
        if ($shipment = $this->_initShipment($order, $data)) {
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
    protected function _initShipment($order, $data) {
        $shipment = false;

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

        if (Mage::helper('barcode')->getCompabilityMode() == '13') {       // Compability with 1.3.x
            $shipment = Mage::getModel('barcode/sales_service_order', $order)->prepareShipment($savedQtys);
        } else {
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
        }

        return $shipment;
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

