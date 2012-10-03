<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Featured
 * @version    3.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Featured_Helper_Onsale extends Mage_Core_Helper_Abstract {
    private $_isOnsale = null;
    
    public function isOnsale() {
        if($this->_isOnsale === null) {
            $_modules = (array) Mage::getConfig()->getNode('modules')->children();
            if(array_key_exists('AW_Onsale', $_modules)
                && 'true' == (string) $_modules['AW_Onsale']->active
                && !(bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_Onsale'))
                $this->_isOnsale = true;
            else 
                $this->_isOnsale = false;
        }
        return $this->_isOnsale;
    }
    
    public function startOnsale($_product, $wh) {
        return '<div class="onsale-category-container-list" style="width:'.$wh.'px;height:'.$wh.'px;">'.Mage::helper('onsale')->getCategoryLabelHtml($_product);
    }
    
    public function endOnsale() {
        return '</div>';
    }
}
