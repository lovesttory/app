<?php
class SM_RMA_Block_Adminhtml_Request_Edit_Tab_Item_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if(!$row->getDone()){
            $item = Mage::getModel('sales/order_item')->load(intval($row->getItemId()));
            
            return '<div style="text-align:center"><input type="checkbox" name="update_stock['.$item->getId().']" value=1 checked="checked" /></div>';
        }
        else{
            return;
        }
    }
}
