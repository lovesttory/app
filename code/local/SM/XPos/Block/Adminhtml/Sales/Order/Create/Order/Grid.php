<?php

class SM_XPos_Block_Adminhtml_Sales_Order_Create_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_filterVisibility = false;
    protected $_headersVisibility = true;
    protected $_pagerVisibility = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_order_grid');
        $this->setRowClickCallback('order.selectOrder.bind(order)');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addAttributeToFilter('status','pending')
            ->addAttributeToSort('created_at','desc')
            ->setPage(1,20);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));
 
        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));
        
        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));        
// 
//        $this->addColumn('status', array(
//            'header' => Mage::helper('sales')->__('Status'),
//            'index' => 'status',
//            'type'  => 'options',
//            'width' => '70px',
//            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
//        ));
  
        return parent::_prepareColumns();
    }

    /**
     * Deprecated since 1.1.7
     */
    public function getRowId($row)
    {
        return $row->getId();
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/xPos/index', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadBlock', array('block'=>'order_grid'));
    }

}
