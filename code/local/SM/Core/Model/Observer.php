<?php

class SM_Core_Model_Observer extends Mage_Core_Model_Abstract {

    // refresh license status after updating
    public function refreshStatus($observer) {
        ob_start();
        $product = split("_", $observer['event']['name']);
        $product = $product[count($product) - 1];
        if ($product == "barcode")
            $product2 = SM_Barcode_Helper_Abstract::PRODUCT;
        else
            $product2 = $product;
        // remove old local key
        $dir = Mage::getBaseDir("var") . DS . "smartosc" . DS . strtolower(substr($product2, 0, 5)) . DS;
        $filepath = $dir . "license.dat";
        $file = new Varien_Io_File;
        $file->rm($filepath);
        Mage::helper('smcore')->checkLicense($product2, Mage::getStoreConfig($product . '/general/key'), true);
        Mage::getConfig()->cleanCache();
    }

}
