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
class SM_Barcode_Adminhtml_Barcode_PrintController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        die('index action');
    }

    public function oneproductAction() {

        $product_id = $this->getRequest()->getParam('id');
        Mage::helper('barcode/barcode')->createProductBarcode($product_id);
        $this->loadLayout()->renderLayout();
    }

    public function multiAction() {
        $data = $this->getRequest()->getPost();
        $print_arr = array();
        $count = 0;
        $errorMessage = array();
        $successProducts = array();
        $product_ids = array();
        foreach ($data as $key => $value) {
            if (preg_match("/product_/i", $key)) {
                $product_id = intval(substr($key, 8));
                $qty = intval($value);
                $product_ids[] = $product_id;
                if ($qty > 0) {
                    $print_arr[$product_id] = $qty;
                }
                //gen barcode image
                if (!Mage::helper('barcode/barcode')->createProductBarcode($product_id)) {
                    $errorMessage[] = "Cannot generate barcode for product " . $product_id;
                } else {
                    $successProducts[] = $product_id;
                    $count++;
                }
            }
        }

        if ($count > 0) {
            Mage::register('successProducts', $successProducts);
            $this->_getSession()->addSuccess(
                    $this->__('Total of %d product(s) have been generated barcode successful.', $count)
            );
        }
        if (count($errorMessage) > 0) {
            foreach ($errorMessage as $value) {
                $this->_getSession()->addError($value);
            }
        }
        Mage::getSingleton('core/session')->setPrintArr($print_arr);
        //show layout to print
        $this->loadLayout()->renderLayout();
    }

    public function showAction() {
        $this->loadLayout()->renderLayout();
    }

}
