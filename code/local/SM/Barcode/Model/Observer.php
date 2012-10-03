<?php
class SM_Barcode_Model_Observer
{
    public function handleProductGridMassaction($observer) 
    {
        $grid = $observer->getGrid();
        if (Mage::helper('barcode')->isEnable()) {
            $grid->getMassactionBlock()->addItem('printbarcode', array(
                'label' => Mage::helper('catalog')->__('Print Barcode'),
                'url' => $grid->getUrl('adminhtml/barcode_product/index', array('_current' => true))
            ));
        }
                
        return $this;
    }
}