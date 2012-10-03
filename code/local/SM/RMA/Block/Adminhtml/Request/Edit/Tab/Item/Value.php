<?php

class SM_RMA_Block_Adminhtml_Request_Edit_Tab_Item_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if (!$row->getDone()) {
            $item = Mage::getModel('sales/order_item')->load(intval($row->getItemId()));
            $amount = Mage::helper('checkout')->getBasePriceInclTax($item) * $row->getQtyToReturn();

//            if ($item->getParentItemId()) {
//                $parentItem = Mage::getModel('sales/order_item')->load($item->getParentItemId());
//
//                $sizes = Mage::helper('rma/item')->getOptions($parentItem);
//                $options = array();
//                if (!$sizes) {
//                    $options[] = '<option value=0>No sizes</option>';
//                } else {
//
//                    foreach ($sizes as $key => $size) {
//                        $options[] = '<option value="' . $key . '">' . $size . '</option>';
//                    }
//                }
//
//                $amount = Mage::helper('checkout')->getBasePriceInclTax($parentItem) * $row->getQtyToReturn();
//            }

            $html = "<div id='processing_" . $row->getItemId() . "' style='display:block;'>n/a</div>
                    <div id='refund_" . $row->getItemId() . "' style='display:none;'>
                       Item Price <input type='text' id='item-price[" . $row->getItemId() . "]' value='{$amount}' disabled class='input-text' /><br/>
                       Handling fee <input type='text' class='input-text' value='0' onkeyup='calcRefund(event,$(\"item-price[" . $row->getItemId() . "]\"),this,$(\"request_value[" . $row->getItemId() . "][1]\"));' /><br/>
                       Refund Amount: <input type='text' onblur='validateAmt(this);' id='request_value[" . $row->getItemId() . "][1]' name='request_value[" . $row->getItemId() . "][1]' value='{$amount}' class='input-text' />
                    </div>
                    <div id='gift_" . $row->getItemId() . "' style='display:none;'>Amount: <input type='text' name='request_value[" . $row->getItemId() . "][2]' value='{$amount}' class='input-text' style='width: 50px;' /></div>
                    ";//<div id='sizes_" . $row->getItemId() . "' style='display:none;'><select name='request_value[" . $row->getItemId() . "][3]'>" . implode('', $options) . "</select></div>";

            $script = "<script type='text/javascript'>
                      function chooseRequestValue(obj, id){
                        if(obj.value*1==1){
                            $('processing_'+id).hide();
//                            $('sizes_'+id).hide();
                            $('refund_'+id).show();
                            $('gift_'+id).hide();
$('exchange-items').hide();                                                        
                        }
                        else if(obj.value*1==2){
                            $('processing_'+id).hide();
//                            $('sizes_'+id).hide();
                            $('refund_'+id).hide();
                            $('gift_'+id).show();
$('exchange-items').hide();                            
                        }
                        else if(obj.value*1==3){
//                            $('processing_'+id).hide();
//                            $('sizes_'+id).show();
//                            $('refund_'+id).hide();
//                            $('gift_'+id).hide();
                            $('processing_'+id).hide();
//                            $('sizes_'+id).hide();
                            $('refund_'+id).show();
                            $('gift_'+id).hide();
$('exchange-items').show();
                        }
                        else{
                            $('processing_'+id).show();
//                            $('sizes_'+id).hide();
                            $('refund_'+id).hide();
                            $('gift_'+id).hide();
$('exchange-items').hide();                                                        
                        }
                    }
function parsef(val){
return isNaN(parseFloat(val))?0:parseFloat(val);
 
}
function calcRefund(event,price,fee,amt){
amt.value = parsef(price.value) + parsef(fee.value);
validateAmt(amt);
}
function validateAmt(e){
e.value = parsef(e.value).toFixed(2);
}
                    </script>
                    ";
            return $html . $script;
        } else {
            return;
        }
    }

}

?>