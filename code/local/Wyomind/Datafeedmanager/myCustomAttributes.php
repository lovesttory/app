<?php

class MyCustomAttributes extends Wyomind_Datafeedmanager_Model_Datafeedmanager{
    
   public function _eval($product,$exp,$value){
       
        // Example of custom attribute
        switch ($exp['pattern']) {

            case "{configurable_qty}":
              
               if($product->type_id=='configurable'){ 
                    // Your custom script
                    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                    $stock_count = 0;
                    foreach ($childProducts as $child)
                        $stock_count+=(int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();

                    /* return false skip the current product */
                    if ($stock_count < 1)   return false;

                    // the value to retrieve
                    return $stock_count;
               }
               else return  false;

           break;
           
           case "{price_rules}":
               
                $price_rule = Mage::getResourceModel('catalogrule/rule');
                $time_stamp = Mage::app()->getLocale()->storeTimeStamp(3);
                $store      = Mage::app()->getStore(3);
                $website_id = $store->getWebsiteId();
                $cust_grp_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
                $rule_price = $price_rule->getRulePrice( $time_stamp, $website_id, $cust_grp_id, $product->getId());
                if($product->getData('special_price') != NULL){
                 $gesprice = sprintf('%.2f',round($product->getData('special_price'),2));
                 } else {
                   if($rule_price === false){
                       $gesprice = sprintf('%.2f',round($product->getPrice(),2));
                   }else{
                       $gesprice = sprintf('%.2f',round($rule_price,2));
                   }
                 }
                return $gesprice;

           
           
           
           /*************** DO NOT CHANGE THESE LINES ***************/
           default :
               return $value;
           break;
           /*************** DO NOT CHANGE THESE LINES ***************/
        }
    }
}