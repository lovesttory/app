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

class AW_Featured_Model_Productimages extends Mage_Core_Model_Abstract {
    public function _construct() {
        $this->_init('awfeatured/productimages');
    }
    
    public function loadBy($_blockId = null, $_productId = null, $_imageId = null) {
        $_collection = $this->getCollection();
        if($_blockId != null)
            $_collection->addBlockFilter($_blockId);
        if($_productId != null)
            $_collection->addProductFilter($_productId);
        if($_imageId != null)
            $_collection->addImageFilter($_imageId);
        foreach($_collection as $_image) {
            return $_image;
        }
        return $this;
    }
}
