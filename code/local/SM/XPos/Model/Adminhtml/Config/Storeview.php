<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class SM_XPos_Model_Adminhtml_Config_Storeview {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $allStores = Mage::app()->getStores();
        $results = array();
        foreach ($allStores as $_eachStoreId => $val) {
            $_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
            $_storeName = Mage::app()->getStore($_eachStoreId)->getName();
            $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
//            echo $_storeId;
//            echo $_storeCode;
//            echo $_storeName;
            $results[] = array('value' => $_storeId, 'label' => Mage::helper('xpos')->__($_storeName));
        }
        return $results;
    }

}
