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


class AW_Featured_Block_Representations_Slider_Block extends AW_Featured_Block_Representations_Common {
    protected function _beforeToHtml() {
        if(!$this->getTemplate()) {
            if($this->getAFPBlockTypeData('layout') == AW_Featured_Model_Representations_Slider_Layouts::LAYOUT_HORIZONTAL)
                $this->setTemplate('aw_featured/slider/horizontal.phtml');
            else
                $this->setTemplate('aw_featured/slider/vertical.phtml');
        }        
        return parent::_beforeToHtml();
    }
    
    public function getContainerStyleString() {
        $res = sprintf('width: %s;', $this->getWidth());
        if($this->getHeight())
            $res .= sprintf('height: %spx;', $this->getHeight());
        return $res;
    }
    
    public function getSwitchEffect() {
        return $this->getAFPBlockTypeData('switcheffect');
    }
    
    public function getWidth() {
        return $this->getAFPBlockTypeData('width') ? $this->getAFPBlockTypeData('width').'px' : '100%';
    }
    
    public function getHeight() {
        return $this->getAFPBlockTypeData('height');
    }
    
    public function getShowProductName() {
        return (bool)$this->getAFPBlockTypeData('showproductname');
    }

    public function getShowDetails() {
        return (bool)$this->getAFPBlockTypeData('showdetails');
    }

    public function getShowPrice() {
        return (bool)$this->getAFPBlockTypeData('showprice');
    }

    public function getShowAddToCartButton() {
        return (bool)$this->getAFPBlockTypeData('showaddtocart');
    }
    
    public function getAnimationSpeed() {
        return $this->getAFPBlockTypeData('animationspeed');
    }

    public function getAutoHideNavi() {
        return $this->getAFPBlockTypeData('autohidenavi');
    }
}
