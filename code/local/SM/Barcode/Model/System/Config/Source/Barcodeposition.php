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
class SM_Barcode_Model_System_Config_Source_Barcodeposition
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label'=>Mage::helper('barcode')->__('Top left')),
            array('value' => '1', 'label'=>Mage::helper('barcode')->__('Top right')),
            array('value' => '2', 'label'=>Mage::helper('barcode')->__('Bottom left')),
            array('value' => '3', 'label'=>Mage::helper('barcode')->__('Bottom right')),
        );
    }

}
