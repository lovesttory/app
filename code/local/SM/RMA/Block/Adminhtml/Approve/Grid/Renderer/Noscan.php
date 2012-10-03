<?php
class SM_RMA_Block_Adminhtml_Approve_Grid_Renderer_Noscan extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return '<div style="text-align:center;"><input onchange="return scanned(this);" name="scanned['. $row->getProductId() . ']" id="scanned['. $row->getProductId() . ']" type="checkbox" /></div>';
    }
}
 
