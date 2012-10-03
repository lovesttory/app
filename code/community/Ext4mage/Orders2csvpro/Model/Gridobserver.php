<?php

class Ext4mage_Orders2csvpro_Model_Gridobserver {

	const XPATH_CONFIG_SETTINGS_IS_ACTIVE		= 'orders2csvpro/settings/is_active';
	
	protected function _isActive() {
		return Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_IS_ACTIVE);
	}
	
    public function massaction($observer) {

        if($this->_isActive() && ($observer->getEvent()->getBlock() instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction ||
        	$observer->getEvent()->getBlock() instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction)) {
            
        	if($observer->getEvent()->getBlock()->getRequest()->getControllerName() =='sales_order') {
		        $observer->getEvent()->getBlock()->addItem('orders2csvpro', array(
		             'label'=> Mage::helper('orders2csvpro')->__('Orders2CSV Pro'),
		             'url'  => Mage::helper('adminhtml')->getUrl('orders2csvpro/admin_orders2csvpro/makecsv'),
		        ));
            }
        }
    }
}