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

class AW_Featured_Block_Widget_Grid_Column_Renderer_Imagepreview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    protected function _getChangeButtonHtml($_showButton, $_pid) {
        if(!$_showButton) return '';
        return '<br /><button type="button" onclick="awfBForm.changeImage('.$_pid.', '.$this->getRequest()->getParam('id').');return false;">'.$this->__('Change...').'</button>';
    }

    public function _getValue(Varien_Object $row) {
        $_errMsg = $this->__('No Image');
        $_canChangeImage = $_imurl = false;
        $_wrapper = '<div class="a-center">%s</div>';
        try {
            $data = parent::_getValue($row);
            if($data) {
                $_product = Mage::getModel('catalog/product')->load($data);
                if($_product->getData()) {
                    $_productImages = Mage::getModel('awfeatured/data_collection')->createFrom($_product->getMediaGalleryImages());
                    $_imurl = Mage::helper('awfeatured/images')->getProductImage($_product, $row->getData('image_id'), 120);
                    $_canChangeImage = (bool) ($_productImages->getSize() > 1 && $this->getRequest()->getParam('id'));
                }
            }
        } catch(Exception $ex) {
            $_imurl = false;
            $_errMsg = $ex->getMessage();
        }
        return sprintf($_wrapper, ($_imurl ? '<img src="'.$_imurl.'" />' : $_errMsg).$this->_getChangeButtonHtml($_canChangeImage, $data));
    }
}
