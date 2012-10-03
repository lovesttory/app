<?php

class SM_RMA_Block_Customer_Requests_New extends Mage_Core_Block_Template {

    protected function _construct() {
        $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);
    }

    public function getCustomer() {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

}
 
