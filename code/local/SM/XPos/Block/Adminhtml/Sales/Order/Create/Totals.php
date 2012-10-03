<?php
class SM_XPos_Block_Adminhtml_Sales_Order_Create_Totals extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected function _getTotalRenderer($code)
    {
        $blockName = $code.'_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/admin_renderer");
            if ($config) {
                $block = (string) $config;
            }            
            $block = $this->getLayout()->createBlock($block, $blockName);
            if($code == "grand_total") $block->setTemplate("sm/xpos/sales/order/create/totals/grandtotal.phtml");
            if($code == "shipping") $block->setTemplate("sm/xpos/sales/order/create/totals/shipping.phtml");
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }    
}
?>
