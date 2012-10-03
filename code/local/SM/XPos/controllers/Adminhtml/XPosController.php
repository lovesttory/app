<?php

require_once(BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'Sales' . DS . 'Order' . DS . 'CreateController.php');

class SM_XPos_Adminhtml_XPosController extends Mage_Adminhtml_Sales_Order_CreateController {

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel() {
        return Mage::getSingleton('xpos/adminhtml_sales_order_create');
    }

    protected $_billingOrigin;
    protected $_validated = false;
    protected $_storeid;

    public function preDispatch() {
        parent::preDispatch();
        if (!Mage::helper('smcore')->checkLicense("xpos", Mage::getStoreConfig('xpos/general/key')) || !Mage::helper("xpos")->isEnableModule()) {
            return $this->_redirect("adminhtml/system_config/edit/section/xpos");
        } else {
            $this->_validated = true;
        }
    }

    protected function _construct() {
        parent::_construct();
        $_billingOrigin = array();
        if (Mage::helper('xpos')->aboveVersion('1.5')) {
            $this->_billingOrigin['city'] = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_CITY);
            $this->_billingOrigin['country_id'] = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID);
            $this->_billingOrigin['region'] = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID);
            $this->_billingOrigin['postcode'] = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE);
        } else {
            $this->_billingOrigin['city'] = Mage::getStoreConfig('shipping/origin/city');
            $this->_billingOrigin['country_id'] = Mage::getStoreConfig('shipping/origin/country_id');
            $this->_billingOrigin['region'] = Mage::getStoreConfig('shipping/origin/region_id');
            $this->_billingOrigin['postcode'] = Mage::getStoreConfig('shipping/origin/postcode');
        }
        $this->_storeid = Mage::getStoreConfig('xpos/general/storeid');
    }

    /**
     * Get requested items qty's from request
     */
    protected function _getItemQtys() {
        $data = $this->getRequest()->getParam('order');
        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = array();
        }
        return $qtys;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder() {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Index page
     */
    public function indexAction() {
        if (!$this->_validated) {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('The xPOS module has been disabled. Please enable to use.'));
//            $this->_redirect('adminhtml/system_config/edit/section/xpos');
            return;
        }
        $orderId = $this->getRequest()->getParam('order_id');
//        $orderId = '';
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
//Fix Chrome's 2 times request.
                if (!isset($_SESSION['xpos_loaded_order_id'])
                        || $_SESSION['xpos_loaded_order_id'] != $order->getID()) {
                    $this->_getSession()->clear();
                    $this->_getSession()->setUseOldShippingMethod(true);
                    $this->_getOrderCreateModel()->initFromOrder($order);
                    $_SESSION['xpos_loaded_order_id'] = $order->getId();
                }
            }
        } else {
            $this->_getSession()->setStoreId($this->_storeid);
            $this->_getSession()->setCustomerId(false);
            $_quoteId = $this->_getOrderCreateModel()->getQuote()->getId();

            $data_default = array(
                'account' => array(
                    'group_id' => 1
                ),
                'billing_address' => array(
                    'firstname' => 'Guest ' . $_quoteId,
                    'lastname' => 'Guest ' . $_quoteId,
                    'street' => array('Guest Address'),
                    'city' => (empty($this->_billingOrigin['city']) ? 'Guest City' : $this->_billingOrigin['city']),
                    'country_id' => (empty($this->_billingOrigin['country_id']) ? 'Guest City' : $this->_billingOrigin['country_id']),
                    'region' => (empty($this->_billingOrigin['region']) ? 'CA' : $this->_billingOrigin['region']),
                    'postcode' => (empty($this->_billingOrigin['postcode']) ? '95064' : $this->_billingOrigin['postcode']),
                    'telephone' => (Mage::getStoreConfig('general/store_information/phone') == "" ? '1234567890' : Mage::getStoreConfig('general/store_information/phone')),
                ),
                'shipping_method' => 'freeshipping_freeshipping'
            );
            $this->_getOrderCreateModel()->importPostData($data_default);
            $this->_getOrderCreateModel()->setShippingAsBilling('on');
            $this->_getOrderCreateModel()->resetShippingMethod(true);
            $this->_getOrderCreateModel()->collectShippingRates();
            $this->_getOrderCreateModel()
                    ->saveQuote();
        }
        $this->_title($this->__('Sales'))->_title($this->__('X-POS Management'));
        $this->loadLayout();


        // trungtq fix "ghost" product bug
        $_SESSION['blankcart'] = true;
        if (count($this->_getOrderCreateModel()->getQuote()->getAllItems()) > 0) {
            $_SESSION['blankcart'] = false;
        }


        $this->_initSession()->renderLayout();
    }

    /**
     * Clear Action
     * 
     */
    public function clearAction() {
        $this->_clear_current_order_cache();
        $this->_getSession()->clear();
        $this->_redirect('*/*/');
    }

    /**
     * Clear Action
     * 
     */
    public function clearcartAction() {
        $quote = $this->_getOrderCreateModel()->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $quote->removeItem($item->getId())->save();
            $this->_getOrderCreateModel()->removeQuoteItem($item);
        }
        $cart = $this->_getOrderCreateModel()->getCustomerCart();
        foreach ($cart->getAllItems() as $item) {
            $cart->removeItem($item->getId())->save();
        }
        $this->_getOrderCreateModel()
                ->saveQuote();
//        if ($quoteID) {
//            try {
//                $quote = Mage::getModel("sales/quote")->load($quoteID);
//                //$quote->setIsActive(false);
//                // $quote->delete();
//                foreach ($quote->getAllItems() as $item) :
//                    $this->_getOrderCreateModel()->_getCart()->removeItem($item->getId())->save();
//                endforeach;
//                echo "cart deleted";
//            } catch (Exception $e) {
//                echo $e->getMessage();
//            }
//        } else {
//            echo "no quote found";
//        }
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()
                        ->save();
                $this->_clear_current_order_cache();
                $this->_getSession()->clear();
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/xPos/index');
        }
//        $this->_clear_current_order_cache();
//        $this->_getSession()->clear();
//        echo "success";
    }

    protected function getCustomerGroups() {
        $groupIds = array();
        $collection = Mage::getModel('customer/group')->getCollection();
        foreach ($collection as $customer) {
            $groupIds[] = $customer->getId();
        }

        return $groupIds;
    }

    protected function _processDiscount($data) {
        $couponCode = false;
        $customDiscount = $this->getRequest()->getPost('custom-discount');
        $customDiscount = floatval($this->getRequest()->getPost('custom-discount'));
        $quote = $this->_getOrderCreateModel()->getQuote();
        if ($customDiscount > 0) {
            $model = Mage::getModel('salesrule/coupon');
            $name = 'X-POS #' . $quote->getId();
            $couponCode = substr(md5($name . microtime()), 0, 8);
            // create coupon
            $rule = Mage::getModel('salesrule/rule')
                    ->setName($name)
                    ->setDescription('Coupon for X-POS')
                    ->setCustomerGroupIds($this->getCustomerGroups())
                    ->setFromDate('2012-07-12')
                    ->setUsesPerCoupon(12)
                    ->setUsesPerCustomer(31)
                    ->setIsActive(1)
                    ->setSimpleAction(Mage_SalesRule_Model_Rule::BY_FIXED_ACTION)
                    ->setDiscountAmount($customDiscount)
                    ->setStopRulesProcessing(0)
                    ->setIsRss(0)
                    ->setWebsiteIds(1)
                    ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
                    ->save();

            $model->setRuleId($rule->getId())
                    ->setCode($couponCode)
                    ->setIsPrimary(1)
                    ->save();        
        
        }

        
        if ($couponCode) {
            $data['coupon']['code'] = $couponCode;
            $this->_getOrderCreateModel()
                    ->importPostData($data);
        }
        return $couponCode ? $rule : false;
    }

    /**
     * Process request data with additional logic for saving quote and creating order
     *
     * @param string $action
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _processData($action = null) {
//2011-12-20 HiepHM: Fix bug auto add product that has product_id = quote item_id
//Bug arised because must pass $action to _processActionData(), so it could be checked in line 193 of CreateController:
// ... && !($action == 'save')) 
        if ($action != 'save') {
            if (Mage::helper('xpos')->aboveVersion('1.5')) {
                parent::_processActionData($action);
            } else {
                parent::_processData();
            }
        }
        if ($action == 'save') {
            $quote = $this->_getOrderCreateModel()->getQuote();
            if ($storeid = $this->getRequest()->getPost('store_id'))
                $quote->setStoreId($storeid);
            else
                $quote->setStoreId($this->_storeid);

            $data = $this->getRequest()->getPost('order');
            $couponCode = $this->_processDiscount($data);

            $shipping_same_as_billing = $this->getRequest()->getPost('shipping_same_as_billing');
            if ($data['customer_id'] != "false" && intval($data['customer_id']))
                $this->_getSession()->setCustomerId((int) $data['customer_id']);

            $billingAddress = array(
                'firstname' => 'Guest ' . $this->_getOrderCreateModel()->getQuote()->getId(),
                'lastname' => 'Guest ' . $this->_getOrderCreateModel()->getQuote()->getId(),
                'street' => array('Guest Address'),
                'city' => (empty($this->_billingOrigin['city']) ? 'Guest City' : $this->_billingOrigin['city']),
                'country_id' => (empty($this->_billingOrigin['country_id']) ? 'Guest City' : $this->_billingOrigin['country_id']),
                'region' => (empty($this->_billingOrigin['region']) ? 'CA' : $this->_billingOrigin['region']),
                'postcode' => (empty($this->_billingOrigin['postcode']) ? '95064' : $this->_billingOrigin['postcode']),
                'telephone' => (Mage::getStoreConfig('general/store_information/phone') == "" ? '1234567890' : Mage::getStoreConfig('general/store_information/phone')),
            );
            if (!empty($data['customer_id']) && ($customer_id = $this->_getSession()->getCustomerId())) {
                if (Mage::getModel('customer/customer')->load($customer_id)->getDefaultBillingAddress())
                    $defaultBillingAddress = Mage::getModel('customer/customer')->load($customer_id)->getDefaultBillingAddress()->getData();
                foreach ($billingAddress as $k => $v) {
                    if (!empty($defaultBillingAddress[$k]))
                        $data['billing_address'][$k] = $defaultBillingAddress[$k];
                }
                if (!empty($defaultBillingAddress['region_id'])) {
                    unset($data['billing_address']['region']);
                    $data['billing_address']['region_id'] = $defaultBillingAddress['region_id'];
                }
            } else {
                foreach ($billingAddress as $k => $v) {
                    if (empty($data['billing_address'][$k]))
                        $data['billing_address'][$k] = $v;
                }
            }
            $quote->getBillingAddress()->addData($data['billing_address']);

            if ($shipping_same_as_billing) {
                $quote->getShippingAddress()->addData($data['billing_address']);
            } else {
                $quote->getShippingAddress()->addData($data['shipping_address']);
            }

            $data['shipping_method'] = "freeshipping_freeshipping";
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                    ->setShippingMethod('freeshipping_freeshipping');

            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $quote->getPayment()->addData($paymentData);
            }

            $this->_getOrderCreateModel()->collectRates();
            $quote->collectTotals();

            $quote->save();

            // remove coupon if has
            if ($couponCode) {
                $couponCode->setIsActive(0)->save();
            }
        }
    }

    /**
     * Cancel Action
     * 
     */
    public function cancelAction() {
        $this->_clear_current_order_cache();
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()
                        ->save();
                $this->_getSession()->clear();
                $this->_getSession()->addSuccess(
                        $this->__('The order has been cancelled.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/xPos/index');
        }
    }

    /**
     * Complete Action
     * 
     */
    public function completeAction() {
        var_dump('complete action');
        die;
        $this->_clear_current_order_cache();
        try {
            $data = $this->getRequest()->getPost('order');
            $this->_processData('save');

            unset($data['shipping_address']);
            unset($data['billing_address']);
            if ($data['account']['type'] == 'new') {
                $data['account']['email'] = $data['account']['email_temp'];
            }
            // fix for EU stores
            if (! $data['account']['taxvat'])
                $data['account']['taxvat'] = '-';
//            $data['shipping_method'] = "freeshipping_freeshipping";
            $this->_getOrderCreateModel()->setRecollect(true);

            $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($data)
                    ->createOrder();

            /* Create Invoice & Shipment */
            $savedQtys = $this->_getItemQtys();

            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
            $invoice->register();

            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
            $shipment->register();

            Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($order)
                    ->addObject($shipment)->save();
            /* Complete the order */
            $totalPaid = $this->getRequest()->getPost('cash-in');
            $totalRefunded = $this->getRequest()->getPost('balance');
            $order
                    ->setTotalPaid($totalPaid)
                    ->setBaseTotalPaid($totalPaid)
                    ->setTotalRefunded($totalRefunded)
                    ->setBaseTotalRefunded($totalRefunded)->save();

            $this->_getSession()->clear();

            $href = Mage::helper("adminhtml")->getUrl('*/xPos/print', array('order_id' => $order->getId()));
            Mage::getSingleton('core/session')->setPrintUrl($href);

            $this->_redirect('*/xPos/index');
            
        } catch (Mage_Payment_Model_Info_Exception $e) {            
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Complete Offline Action - for submitting offline orders
     * 
     */
    public function completeofflineAction() {
//        var_dump('aaa');
//        die;
        $this->_clear_current_order_cache();

        try {
            $data = $this->getRequest()->getPost('order');
            $couponCode = $this->_processDiscount($data);
            $shipping_same_as_billing = $this->getRequest()->getPost('shipping_same_as_billing');
            if ($data['account']['email_temp'])
                $data['account']['email'] = $data['account']['email_temp'];
            if ($data['customer_id'] != "false" && intval($data['customer_id']))
                $this->_getSession()->setCustomerId((int) $data['customer_id']);
            if (! $data['account']['taxvat'])
                $data['account']['taxvat'] = '-';
            $quote = $this->_getOrderCreateModel()->getQuote();
            // set store
            if ($storeid = $this->getRequest()->getPost('store_id'))
                $quote->setStoreId($storeid);
            else
                $quote->setStoreId($this->_storeid);            
            // set billing address            
            $billingAddress = $quote->getBillingAddress()->addData($data['billing_address']);
            $billingAddress = array(
                'firstname' => 'Guest ' . $quote->getId(),
                'lastname' => 'Guest ' . $quote->getId(),
                'street' => array('Guest Address'),
                'city' => (empty($this->_billingOrigin['city']) ? 'Guest City' : $this->_billingOrigin['city']),
                'country_id' => (empty($this->_billingOrigin['country_id']) ? 'Guest City' : $this->_billingOrigin['country_id']),
                'region' => (empty($this->_billingOrigin['region']) ? 'CA' : $this->_billingOrigin['region']),
                'postcode' => (empty($this->_billingOrigin['postcode']) ? '95064' : $this->_billingOrigin['postcode']),
                'telephone' => (Mage::getStoreConfig('general/store_information/phone') == "" ? '1234567890' : Mage::getStoreConfig('general/store_information/phone')),
            );
            $addressData = array(
                'firstname' => 'Guest ' . $quote->getId(),
                'lastname' => 'Guest ' . $quote->getId(),
                'street' => array('Guest Address'),
                'city' => 'Somewhere',
                'postcode' => '123456',
                'telephone' => '123456',
                'country_id' => 'US',
                'region_id' => 12, // id from directory_country_region table
            );

            if (!empty($data['customer_id']) && ($customer_id = $this->_getSession()->getCustomerId())) {
                $tmp = Mage::getModel('customer/customer')->load($customer_id)->getDefaultBillingAddress();
                if ($tmp)
                    $defaultBillingAddress = $tmp->getData();
                else
                    $defaultBillingAddress = $billingAddress;
                foreach ($billingAddress as $k => $v) {
                    if (!empty($defaultBillingAddress[$k]))
                        $data['billing_address'][$k] = $defaultBillingAddress[$k];
                }

                if (!empty($defaultBillingAddress['region_id'])) {
                    unset($data['billing_address']['region']);
                    $data['billing_address']['region_id'] = $defaultBillingAddress['region_id'];
                }
            } else {
                foreach ($billingAddress as $k => $v) {
                    if (empty($data['billing_address'][$k]))
                        $data['billing_address'][$k] = $v;
                }
            }
            $billingAddress = $quote->getBillingAddress()->addData($data['billing_address']);
            if ($data['account']['type'] == 'new') {
                $quote->setCustomerEmail($data['account']['email_temp']);
                if ($shipping_same_as_billing) {
                    $shippingAddress = $quote->getShippingAddress()->addData($data['billing_address']);
                }
                $quote->save();
            }

            if ($data['account']['type'] != 'guest' && !$shipping_same_as_billing) {
                $shippingAddress = $quote->getShippingAddress()->addData($data['shipping_address']);
            } else {
                $shippingAddress = $quote->getShippingAddress()->addData($data['billing_address']);
            }


            if ($data['account']['type'] == 'exist') {
                $customer = Mage::getModel('customer/customer')
                        ->setWebsiteId(1)
                        ->loadByEmail($data['account']['email']);
                $quote->assignCustomer($customer);
                // billing address
//                if ($customer->getDefaultBillingAddress())
//                    $quote->getBillingAddress()->addData( $customer->getDefaultBillingAddress()->getData());

                if ($shipping_same_as_billing) {
                    if ($customer->getDefaultBillingAddress())
                        $addressData = $customer->getDefaultBillingAddress()->getData();
                    else {
                        $addressData['firstname'] = $customer->getFirstname();
                        $addressData['lastname'] = $customer->getLastname();
                    }
                } else {
                    $addressData = $data['shipping_address'];
                }
                $shippingAddress = $quote->getShippingAddress()->addData($addressData);
//                    $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
//                            ->setShippingMethod('freeshipping_freeshipping');                
            }
            if ($data['account']['type'] == 'guest') {
// for guest orders only:
                $quote->setCustomerEmail('guest' . $quote->getId() . '@example.com');
            }
            $quoteItems = $quote->getAllItems();
            foreach ($quoteItems as $item) {
                $quote->removeItem($item->getId());
            }
            $quoteItems = $this->getRequest()->getPost('item');
//$this->_getOrderCreateModel()->addProducts($quoteItems);
            $items = array();
            foreach ($quoteItems as $item => $itemdetails) {
                if ($itemdetails['qty'] != 0)
                    $items[$item] = $itemdetails;
            }
            $items = $this->_processFiles($items);
            $this->_getOrderCreateModel()->addProducts($items);
            $this->_getOrderCreateModel()->updateCustomPrice($quoteItems);

///            $this->_getOrderCreateModel()->collectShippingRates();
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $quote->getPayment()->addData($paymentData);
            }

            $this->_getOrderCreateModel()->collectRates();
            $this->_getOrderCreateModel()
                    ->saveQuote();

            unset($data['shipping_address']);
            unset($data['billing_address']);
//            unset($data['customer_id']);
//            unset($data['account']);
            $data['shipping_method'] = "freeshipping_freeshipping";
            $this->_getOrderCreateModel()->setRecollect(true);
            $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($data)
                    ->createOrder();

            /* Create Invoice & Shipment */
            $savedQtys = $this->_getItemQtys();

            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
            $invoice->register();

            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
            $shipment->register();
            /* Complete the order */
            $order
                    ->setTotalPaid($this->getRequest()->getPost('cash-in'))
                    ->setBaseTotalPaid($this->getRequest()->getPost('cash-in'))
                    ->setTotalRefunded($this->getRequest()->getPost('balance'))
                    ->setBaseTotalRefunded($this->getRequest()->getPost('balance'));

            Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($order)
                    ->addObject($shipment)->save();

            $this->_getSession()->clear();
            $result = array(
                'status' => 'success',
                'msg' => '',
                'printurl' => Mage::helper("adminhtml")->getUrl('*/xPos/print', array('order_id' => $order->getId()))
            );
            // remove coupon if has
            if ($couponCode) {
                $couponCode->setIsActive(0)->save();
            }
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        }
        echo json_encode($result);
    }

    /**
     * Save Action
     * 
     */
    public function saveAction() {
        var_dump('save action');
        die;
        $this->_clear_current_order_cache();
        try {
            $this->_processData('save');
            $data = $this->getRequest()->getPost('order');
            unset($data['billing_address']);
            unset($data['shipping_address']);
            $order = $this->_getOrderCreateModel()
                    ->setIsValidate(true)
                    ->importPostData($data)
                    ->createOrder();
            if ($data['account']['email_temp'] && $data['account']['email_temp'] != "") {
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customer->setEmail($data['account']['email_temp'])->save();
            }
            $this->_getSession()->clear();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
            $this->_redirect('*/*/index');
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Print Invoice Action
     *
     */
    public function printAction() {
        if ($order = $this->_initOrder()) {
            $this->_title("Invoice No." . $order->getRealOrderId());
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    /**
     * Product Search Action
     *
     */
    public function productSearchAction() {
        $items = array();

        $start = $this->getRequest()->getParam('start', 1);
        $limit = $this->getRequest()->getParam('limit', 10);
        $query = $this->getRequest()->getParam('query', '');

        $searchInstance = new SM_XPos_Model_Adminhtml_Search_Catalog;

        $results = $searchInstance->setStart($start)
                ->setLimit($limit)
                ->setQuery($query)
                ->load()
                ->getResults();

        $items = array_merge_recursive($items, $results);

        $totalCount = sizeof($items);

        $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('sm/xpos/system/autocomplete.phtml')
                ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function productLoadAction() {
        $items = array();
        $limit = $this->getRequest()->getParam('limit', 10);
        $page = $this->getRequest()->getParam('page', 1);
        $searchInstance = new SM_XPos_Model_Adminhtml_Search_Catalog;

        $results = $searchInstance
                ->loadAll($limit, $page);

        if ($results)
            $items = $results->getResults();

        $this->getResponse()->setBody(json_encode($items));
    }

    /**
     * Customer Search Action
     *
     */
    public function customerSearchAction() {
        $items = array();

        $start = $this->getRequest()->getParam('start', 1);
        $limit = $this->getRequest()->getParam('limit', 10);
        $query = $this->getRequest()->getParam('query', '');

        $searchInstance = new SM_XPos_Model_Adminhtml_Search_Customer;

        $results = $searchInstance->setStart($start)
                ->setLimit($limit)
                ->setQuery($query)
                ->load()
                ->getResults();

        $items = array_merge_recursive($items, $results);

        $totalCount = sizeof($items);

        $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('sm/xpos/system/autocomplete.phtml')
                ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function customerLoadAction() {
        $limit = $this->getRequest()->getParam('limit', 10);
        $page = $this->getRequest()->getParam('page', 1);
        $items = array();

        $searchInstance = new SM_XPos_Model_Adminhtml_Search_Customer;
        $results = $searchInstance
                ->loadAll($limit, $page);
        if ($results)
            $items = $results->getResults();

        $this->getResponse()->setBody(json_encode($items));
    }

    public function checkNetworkAction() {
        die("ok");
    }

    /**
     * Load Block Action
     * 
     */
    public function loadBlockAction() {
        $request = $this->getRequest();
        try {
            $this->_initSession()
                    ->_processData();
        } catch (Mage_Core_Exception $e) {
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_reloadQuote();
            $this->_getSession()->addException($e, $e->getMessage());
        }


        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_xpos_load_block_json');
        } else {
            $update->addHandle('adminhtml_xpos_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_xpos_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    /**
     * Show item update result from loadBlockAction
     * to prevent popup alert with resend data question
     *
     */
    public function showUpdateResultAction() {
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->hasUpdateResult() && is_scalar($session->getUpdateResult())) {
            $this->getResponse()->setBody($session->getUpdateResult());
            $session->unsUpdateResult();
        } else {
            $session->unsUpdateResult();
            return false;
        }
    }

    protected function _clear_current_order_cache() {
        unset($_SESSION['xpos_loaded_order_id']);
    }

    /**
     * Customer Search Action
     *
     */
    public function checkAliveAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setBody('ok');
    }
    
    protected function _getLastId() {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $storeid = Mage::getStoreConfig('xpos/general/storeid');
        $queryLastId = "SELECT increment_last_id FROM  eav_entity_store
          inner join eav_entity_type on eav_entity_type.entity_type_id = eav_entity_store.entity_type_id
          and eav_entity_type.entity_type_code='order' and eav_entity_store.store_id = {$storeid}";

        $lastid = $readConnection->fetchOne($queryLastId);
        return $lastid;
    }

    public function getorderidAction() {

        $this->getResponse()->setBody(self::_getLastId());
    }

    public function resetnumberAction() {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');

        // Get last id before change
        $lastid = self::_getLastId();
        $this->_lastIdBeforeChange = $lastid;
        $lastid = intval($lastid) - intval($lastid)%1000000 + 1000000;

        $storeid = Mage::getStoreConfig('xpos/general/storeid');

        // Apply change for last id
        $query = "update eav_entity_store
                inner join eav_entity_type on eav_entity_type.entity_type_id = eav_entity_store.entity_type_id
                set eav_entity_store.increment_last_id='{$lastid}'
                where eav_entity_type.entity_type_code='order' and eav_entity_store.store_id = {$storeid} ";
        $writeConnection->query($query);

        // Get last id after changed
        $lastidChk = self::_getLastId();
        // Check last id before change and after change
        if (strcmp($lastid,$lastidChk) == 0)
        $this->getResponse()->setBody(true);
        else {
            $this->getResponse()->setBody(false);
        }
    }
}

