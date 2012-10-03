<?php

class SM_XPos_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('sales')->__('Orders');
        parent::__construct();
        $this->_removeButton('add');
    }

}
