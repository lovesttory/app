<?php

class Ext4mage_Orders2csvpro_Block_Adminhtml_Help_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('help_tabs');
		//$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('orders2csvpro')->__('Order2CSV PRO Help'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('generel_section', array(
          'label'     => Mage::helper('orders2csvpro')->__('Order'),
          'title'     => Mage::helper('orders2csvpro')->__('All order variables'),
          'content'   => $this->getLayout()->createBlock('orders2csvpro/adminhtml_help_tab_general')->toHtml(),
		));
		$this->addTab('product_section', array(
          'label'     => Mage::helper('orders2csvpro')->__('Product'),
          'title'     => Mage::helper('orders2csvpro')->__('Product variables'),
          'content'   => $this->getLayout()->createBlock('orders2csvpro/adminhtml_help_tab_product')->toHtml(),
		));
		$this->addTab('productbundle_section', array(
          'label'     => Mage::helper('orders2csvpro')->__('Product Bundle'),
          'title'     => Mage::helper('orders2csvpro')->__('Product Bundle variables'),
          'content'   => $this->getLayout()->createBlock('orders2csvpro/adminhtml_help_tab_productbundle')->toHtml(),
		));
		$this->addTab('customer_section', array(
          'label'     => Mage::helper('orders2csvpro')->__('Customer'),
          'title'     => Mage::helper('orders2csvpro')->__('Order Customer variables'),
          'content'   => $this->getLayout()->createBlock('orders2csvpro/adminhtml_help_tab_customer')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}