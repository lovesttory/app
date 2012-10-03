<?php

class SM_RMA_Block_Adminhtml_Approve_Grid_Renderer_Valid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $request = Mage::getModel('rma/request')->getCollection()
                ->addFieldToFilter('main_table.status', array('neq' => SM_RMA_Model_Request::STATUS_PENDING_APPROVAL))
        ;
        $request->getSelect()->join(array('items' => 'sm_rma_items'), 'items.rma_id=main_table.id')
                ->where('items.item_id=' . $row->getId())
        ;

        $qty_returned = 0;
        if ($request->getSize()) {
            foreach ($request as $value) {
                $qty_returned += intval($value->getQtyToReturn());
            }
        }

        if ($row->getParentItemId()) {
            $product = Mage::getModel('sales/order_item')->load($row->getParentItemId());
        } else {
            $product = Mage::getModel('sales/order_item')->load($row->getItemId());
        }

        $img_valid = '';
        if ($qty_returned === intval($product->getQtyShipped())) {
            $img_valid = '<img src="' . $this->getSkinUrl('images/ico_success.gif') . '" width="16px" height="16px" alt="Valid" />';
        }
        return '<div id="qty-valid-' . $row->getProductId() . '" style="text-align:center;">' . $img_valid . '</div>';
    }

}
