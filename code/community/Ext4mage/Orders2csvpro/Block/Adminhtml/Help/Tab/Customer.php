<?php

class Ext4mage_Orders2csvpro_Block_Adminhtml_Help_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
	const XPATH_CONFIG_SETTINGS_ORDER_ID		= 'orders2csvpro/settings/order_id';
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('helpCustomerGrid');
		$this->setFilterVisibility(false);
		$this->setPagerVisibility(false);
	}

	protected function _prepareCollection()
	{
		$collection = new Varien_Data_Collection();
		$orderId = Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_ORDER_ID);
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

		if(!$order->getData() && count($order->getData())==0){
			$row = new Varien_Object(array('key'=>"order_data_?", 'value'=>'Selected order not present',
      						'object'=>'Order data'));
			$collection->addItem($row);
			$this->setCollection($collection);
			return parent::_prepareCollection();
		}

		$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		foreach ($customer->getData() as $key => $value) {
			$row = new Varien_Object(array('key'=>"customer_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Customer data'));
			$collection->addItem($row);
		}

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('object', array(
	      'header'    => Mage::helper('orders2csvpro')->__('Object'),
	      'align'     =>'left',
	      'index'     => 'object',
	      'sortable'  => false,
    	  'type'      => 'text',
          'width'     => '20%'
		));
		$this->addColumn('variable', array(
          'header'    => Mage::helper('orders2csvpro')->__('Key'),
          'align'     =>'left',
          'index'     => 'key',
	      'sortable'  => false,
    	  'type'      => 'text',
          'width'     => '40%'
		));
		$this->addColumn('value', array(
          'header'    => Mage::helper('orders2csvpro')->__('Exampel value'),
          'align'     =>'left',
          'index'     => 'value',
	      'sortable'  => false,
    	  'type'      => 'text',
          'width'     => '40%'
		));

		$this->addExportType('*/*/exportCustomerCsv', Mage::helper('orders2csvpro')->__('CSV'));
		$this->addExportType('*/*/exportCustomerXml', Mage::helper('orders2csvpro')->__('XML'));

		return parent::_prepareColumns();
	}

	
	/**
	* Get row edit url
	*
	* @return string
	*/
	public function getRowUrl($row)
	{
		return false;
		//return $this->getUrl('*/*/edit', array('type'=>$row->getId()));
	}
}