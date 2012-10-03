<?php
class SM_XPos_Block_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Block_Sales_Order_Create
{
    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock('xpos/adminhtml_catalog_product_composite_configure')->toHtml();
        return $html;
    }
    
}
?>
