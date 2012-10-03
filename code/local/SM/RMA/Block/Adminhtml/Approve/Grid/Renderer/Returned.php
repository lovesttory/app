<?php

class SM_RMA_Block_Adminhtml_Approve_Grid_Renderer_Returned extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

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

        return '<div id="qty-returned-' . $row->getProductId() . '" style="text-align:center;">' . $qty_returned . '</div>';
    }

}
