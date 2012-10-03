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
class SM_Barcode_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
	protected function _prepareMassaction()
    {
		parent::_prepareMassaction();
		if(Mage::helper('barcode')->isEnable()){
            $this->getMassactionBlock()->addItem('printbarcode', array(
                'label' => Mage::helper('catalog')->__('Print Barcode'),
                'url'   => $this->getUrl('adminhtml/barcode_product/index', array('_current'=>true))
            ));
        }
		
		return $this;
	}

}
