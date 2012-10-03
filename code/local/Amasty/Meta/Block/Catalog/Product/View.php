<?php
/**
 * @copyright   Copyright (c) 2011 Amasty (http://www.amasty.com)
 */  
class Amasty_Meta_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        $product = $this->getProduct();
	    if (!$product){
            return parent::_prepareLayout();
        }
        
        if (!Mage::getStoreConfig('ammeta/product/enabled'))
            return parent::_prepareLayout();
            
        $hlp = Mage::helper('ammeta');    

        //templates configuration for products in categories
        $config = $hlp->getConfigByProduct($product);
        
        // product attribute => template name
        $pairs = array(
            'meta_title'        => 'title',
            'meta_description'  => 'description',
            'meta_keyword'      => 'keywords',
            'short_description' => 'short_description',
            'description'       => 'full_description',
            
        );
        foreach ($pairs as $attrCode => $patternName){
            
            if ($product->getData($attrCode)){
                continue;
            }
            
            $pattern = Mage::getStoreConfig('ammeta/product/' . $patternName);
            foreach ($config as $item){
                if ($item->getData($patternName)){
                    // get first not empty pattern
                    $pattern = $item->getData($patternName);
                    break;
                }    
            }
            
            if ($pattern) {
                $product->setData($attrCode, $hlp->parse($product, $pattern));
            }
            
        }
        
        return parent::_prepareLayout();
    }
}