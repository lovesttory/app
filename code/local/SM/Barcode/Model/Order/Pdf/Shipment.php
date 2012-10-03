<?php

/**
 * SmartOSC Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * 
 * @category   SM
 * @package    SM_Barcode
 * @version    2.0
 * @author     hoadx@smartosc.com
 * @copyright  Copyright (c) 2010-2011 SmartOSC Co. (http://www.smartosc.com)
 */
class SM_Barcode_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment {

    protected function insertOrder(&$page, $obj, $putOrderId = true) {
        parent::insertOrder($page, $obj, $putOrderId);
        if (Mage::helper('barcode')->isEnable() && Mage::helper('barcode')->canShowOnPackingslip()) {
            if (!Mage::helper('smcore')->checkLicense(SM_Barcode_Helper_Abstract::PRODUCT, Mage::getStoreConfig('barcode/general/key')))
                exit();
            if ($obj instanceof Mage_Sales_Model_Order) {
                $shipment = null;
                $order = $obj;
            } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
                $shipment = $obj;
                $order = $shipment->getOrder();
            }

            $image = Mage::helper('barcode/barcode')->createOrderBarcode($order->getRealOrderId());
            if (is_file($image)) {
                $imageWidth = intval(Mage::getStoreConfig('barcode/order/barcode_width')) > 0 ? intval(Mage::getStoreConfig('barcode/order/barcode_width')) : 90;
                $imageHeight = intval(Mage::getStoreConfig('barcode/order/barcode_height')) > 0 ? intval(Mage::getStoreConfig('barcode/order/barcode_height')) : 30;
                $image = Zend_Pdf_Image::imageWithPath($image);
                if (intval(Mage::getStoreConfig('barcode/order/packingslip_position'))==3){
                    // BOTTOM RIGHT
                    $top = $imageHeight*2;
                    $left = intval(Mage::getStoreConfig('barcode/order/padding_left')) > 0 ? intval(Mage::getStoreConfig('barcode/order/padding_left')) : 482;
                } elseif (intval(Mage::getStoreConfig('barcode/order/packingslip_position'))==2){
                    // BOTTOM LEFT
                    $top = $imageHeight*2;
                    $left = 25;
                } elseif (intval(Mage::getStoreConfig('barcode/order/packingslip_position'))==0){
                    // TOP LEFT
                    $top = intval(Mage::getStoreConfig('barcode/order/padding_top')) > 0 ? 825 - intval(Mage::getStoreConfig('barcode/order/padding_top')) : 825;
                    $left = 25;
                } else{
                    // TOP RIGHT
                    $top = intval(Mage::getStoreConfig('barcode/order/padding_top')) > 0 ? 825 - intval(Mage::getStoreConfig('barcode/order/padding_top')) : 825;
                    $left = intval(Mage::getStoreConfig('barcode/order/padding_left')) > 0 ? intval(Mage::getStoreConfig('barcode/order/padding_left')) : 482;                    
                }
                
                $page->drawImage($image, $left, $top - $imageHeight, $left + $imageWidth, $top);
            }
            // start drawing logo
            if (Mage::getStoreConfig("barcode/order/include_logo")) {
                $logoFile = is_file("media/barcode/" . Mage::getStoreConfig("barcode/product/logo_image_file")) ? "media/barcode/" . Mage::getStoreConfig("barcode/product/logo_image_file") : "media/barcode/logo.png";
                $logoType = exif_imagetype($logoFile);
                if ($logoType == 1) {
                    //gif not supported
                    $logoFile = "media/barcode/logo.png";
                } elseif ($logoType == 2) {
                    //jpeg is ok
                } elseif ($logoType == 3) {
                    //png is ok
                } else {
                    //other types
                    $logoFile = "media/barcode/logo.png";
                }

                $logoSize = getimagesize($logoFile);
                $logoFileWidth = intval($logoSize["0"]);
                $logoFileHeight = intval($logoSize["1"]);

                // resize logo
                $availableHeight = $imageHeight;
                $availableWidth = $imageWidth;

                if ($logoFileHeight > $availableHeight) {
                    $logoHeight = $availableHeight;
                    $logoWidth = $logoFileWidth
                            * $logoHeight / $logoFileHeight;
                } else {
                    $logoWidth = intval(Mage::getStoreConfig("barcode/product/logo_width")) > 0 ? intval(Mage::getStoreConfig("barcode/product/logo_width")) : $logoSize["0"];
                    $logoHeight = intval(Mage::getStoreConfig("barcode/product/logo_height")) > 0 ? intval(Mage::getStoreConfig("barcode/product/logo_height")) : $logoSize["1"];
                }
                // 2nd check
                if ($logoHeight > $availableHeight) {
                    $logoHeight = $availableHeight;
                    $logoWidth = $logoFileWidth
                            * $logoHeight / $logoFileHeight;
                }
                // check if posible to display logo
                if ($logoHeight < 0 || $logoWidth < 0)
                    $logoHeight = $logoWidth = 0;
                // final check before drawing
                if (is_file($logoFile)) {
                    $imageLogo = Zend_Pdf_Image::imageWithPath($logoFile);
                    if ($left!=25)
                        $left = 25;
                    else 
                        $left = 570 - $logoWidth;
                    $top -= $logoHeight;
                    //$page->drawImage($image, $left, $bottom, $right, $top);
                    $page->drawImage($imageLogo, $left, $top, $left + $logoWidth, $top + $logoHeight);
                }
            }
            // end drawing logo            
        }
    }

}

