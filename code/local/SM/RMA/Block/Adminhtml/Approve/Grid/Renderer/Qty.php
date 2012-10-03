<?php
class SM_RMA_Block_Adminhtml_Return_Grid_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return '<input type="text" id="qty-scanned-'.$row->getProductId().'" name="qty_scanned_'.$row->getProductId().'" value="0" class="input-text" style="text-align:center;" readonly="readonly" />';
    }
}
 
