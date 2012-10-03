<?php

class SM_RMA_Block_Adminhtml_Approve_Grid_Renderer_Ordered extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if ($row->getParentItemId()) {
            $product = Mage::getModel('sales/order_item')->load($row->getParentItemId());
        } else {
            $product = Mage::getModel('sales/order_item')->load($row->getItemId());
        }
        return '<div id="qty-ordered-' . $row->getProductId() . '" style="text-align:center;">' . intval($product->getQtyOrdered()) . '</div>';
    }

}
