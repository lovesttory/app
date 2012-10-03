<?php

class SM_RMA_Block_Adminhtml_Approve_Grid_Renderer_Scanned extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        return '<div id="qty-scanned-' . $row->getProductId() . '" style="text-align:center;">0</div><input type="hidden" id="qty-scanned-hidden-' . $row->getProductId() . '" name="items[' . $row->getId() . ']" value="0" />';
    }

}
