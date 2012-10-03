<?php

class Ext4mage_Orders2csvpro_Block_Adminhtml_Help_Tab_General extends Mage_Adminhtml_Block_Widget_Grid
{
	const XPATH_CONFIG_SETTINGS_ORDER_ID		= 'orders2csvpro/settings/order_id';
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('help_general');
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

		foreach ($order->getData() as $key => $value) {
			$row = new Varien_Object(array('key'=>"order_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Order data'));
			$collection->addItem($row);
		}

		foreach ($order->getBaseCurrency()->getData() as $key => $value){
			$row = new Varien_Object(array('key'=>"order_base_currency_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Order Base Currency'));
			$collection->addItem($row);
		}

		foreach ($order->getOrderCurrency()->getData() as $key => $value){
			$row = new Varien_Object(array('key'=>"order_currency_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Order Currency'));
			$collection->addItem($row);
		}

		if($order->getPayment()){
			foreach ($order->getPayment()->getData() as $key => $value){
				$row = new Varien_Object(array('key'=>"order_payment_data_$key", 'value'=>htmlentities($value),
	      						'object'=>'Order Payment'));
				$collection->addItem($row);
			}
		}else{
			$row = new Varien_Object(array('key'=>"order_payment_data_?", 'value'=>'none avalible on order',
            						'object'=>'Order Payment'));
			$collection->addItem($row);
		}

		if($order->getPayment() && $order->getPayment()->getAuthorizationTransaction()){
			foreach ($order->getPayment()->getAuthorizationTransaction()->getData() as $key => $value){
				$row = new Varien_Object(array('key'=>"order_payment_auth_trans_data_$key", 'value'=>htmlentities($value),
	      						'object'=>'Order Payment Auth'));
				$collection->addItem($row);
			}
		}else{
			$row = new Varien_Object(array('key'=>"order_payment_auth_trans_data_?", 'value'=>'none avalible on order',
      						'object'=>'Order Payment Auth'));
			$collection->addItem($row);
		}

		if($order->getShipmentsCollection() && count($order->getShipmentsCollection())>0 && $order->getShippingCarrier()){
			foreach ($order->getShippingCarrier()->getData() as $key => $value){
				$row = new Varien_Object(array('key'=>"order_shipping_carrier_data_$key", 'value'=>htmlentities($value),
	      						'object'=>'Order Shipping Carrier'));
				$collection->addItem($row);
			}
		}else{
			$row = new Varien_Object(array('key'=>"order_shipping_carrier_data_?", 'value'=>'none avalible on order',
                  						'object'=>'Order Shipping Carrier'));
			$collection->addItem($row);
		}

		foreach ($order->getStore()->getData() as $key => $value){
			$row = new Varien_Object(array('key'=>"order_store_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Order Store'));
			$collection->addItem($row);
		}

		foreach ($order->getStore()->getGroup()->getData() as $key => $value){
			$row = new Varien_Object(array('key'=>"order_store_group_data_$key", 'value'=>htmlentities($value),
      						'object'=>'Order Store Group'));
			$collection->addItem($row);
		}

		$replacements = array (
     		'order_customer_group'=>'$order->getCustomerGroupId())->getCode()',
			'order_shipping_description'=>'$order->getShippingDescription()',
			'order_payment_block'=>'preg_replace(\'{{pdf_row_separator}}\', \'<br />\', Mage::helper(\'payment\')->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf())',
			'order_base_total_due'=>'$order->getBaseTotalDue()',
			'order_created_full'=>'$order->getCreatedAtFormated("full")',
			'order_created_long'=>'$order->getCreatedAtFormated("long")',
			'order_created_medium'=>'$order->getCreatedAtFormated("medium")',
			'order_created_short'=>'$order->getCreatedAtFormated("short")',
			'order_email_customer_note'=>'$order->getEmailCustomerNote()',
			'order_is_not_virtual'=>'$order->getIsNotVirtual()',
			'order_real_id'=>'$order->getRealOrderId()',
		//'order_shipping_carrier_code'=>'$order->getShippingCarrier()->getCarrierCode()',
			'order_status_label'=>'$order->getStatusLabel()',
			'order_store_url'=>'$order->getStore()->getUrl()',
			'order_store_base_url'=>'$order->getStore()->getBaseUrl()',
    		'order_num_invoices' => '$order->hasInvoices()',
    		'order_num_shipments' => '$order->hasShipments()',
    		'order_num_creditmemos' => '$order->hasCreditmemos()'
		);

		foreach ($replacements as $key => $value) {
			ob_start();
			$evaValue = eval("return ($value);");
			ob_end_clean();
			 
			$row = new Varien_Object(array('key'=>$key, 'value'=>htmlentities($evaValue),
      						'object'=>'Order '));
			$collection->addItem($row);
		}

		if($order->getBillingAddress()){
			foreach ($order->getBillingAddress()->getData() as $key => $value){
				$row = new Varien_Object(array('key'=>"order_billing_data_$key", 'value'=>htmlentities($value),
	      						'object'=>'Order Billing'));
				$collection->addItem($row);
			}
			$row = new Varien_Object(array('key'=>"order_billing_country_name", 'value'=>$order->getBillingAddress()->getCountryModel()->getName(),
	      						'object'=>'Order Billing'));
			$collection->addItem($row);
		}else{
			$row = new Varien_Object(array('key'=>"order_billing_data_?", 'value'=>'none avalible on order',
                  						'object'=>'Order Billing'));
			$collection->addItem($row);
		}


		if($order->getShippingAddress()){
			foreach ($order->getShippingAddress()->getData() as $key => $value){
				$row = new Varien_Object(array('key'=>"order_shipping_data_$key", 'value'=>htmlentities($value),
	      						'object'=>'Order Shipping'));
				$collection->addItem($row);
			}
			$row = new Varien_Object(array('key'=>"order_shipping_country_name", 'value'=>$order->getShippingAddress()->getCountryModel()->getName(),
	      						'object'=>'Order Shipping'));
			$collection->addItem($row);
		}else{
			$row = new Varien_Object(array('key'=>"order_shipping_data_?", 'value'=>'none avalible on order',
                  						'object'=>'Order Shipping'));
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

		$this->addExportType('*/*/exportGeneralCsv', Mage::helper('orders2csvpro')->__('CSV'));
		$this->addExportType('*/*/exportGeneralXml', Mage::helper('orders2csvpro')->__('XML'));

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