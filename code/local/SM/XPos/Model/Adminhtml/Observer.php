<?php
class SM_XPos_Model_Adminhtml_Observer extends Mage_Core_Model_Abstract
{
    public function checkLicense($observer) 
    {
        if (Mage::getStoreConfig('xpos/general/enabled')) {
            if(!Mage::helper('smcore')->checkLicense('xpos', Mage::getStoreConfig('xpos/general/key'))) {
                $block = Mage::app()->getLayout()->createBlock('xpos/adminhtml_license', 'block-license');
                $contentBlock = Mage::app()->getLayout()->getBlock('content');
                if(get_class($contentBlock) == 'SM_XPos_Block_Adminhtml_Sales_Order_Create') {
                    $contentBlock = Mage::app()->getLayout()->getBlock('order_item_extra_info');
                }
                $contentBlock->append($block);
            }            
        }
    }
}
?>