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

class AW_Featured_Block_Widget_Grid_Column_Renderer_Galleryimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        $file = $row->getData('url');
        $_html = '';
        if($data && $file) {
            $_imageAddress = Mage::app()->getStore()->getConfig(Mage::helper('awfeatured')->isHttps() ? Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL : Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
            $_imageUrl = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.Mage::helper('awfeatured/images')->getFolderName().DS.Mage::helper('awfeatured/images')->imageResizeRemote($file, $data);
            if($_imageUrl)
                $_html .= '<div class="a-center"><img src="'.$_imageAddress.$_imageUrl.'" /></div>';
        }
        return $_html;
    }
}
