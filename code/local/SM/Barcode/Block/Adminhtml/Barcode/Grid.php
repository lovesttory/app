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
class SM_Barcode_Block_Adminhtml_Barcode_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct(){
		parent::__construct();
		$this->setId('barcodeGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
    {
        if($this->getRequest()->getPost('product')){
            Mage::getSingleton('core/session')->setBarcodeProduct();
            $product_ids = $this->getRequest()->getPost('product');
            Mage::getSingleton('core/session')->setBarcodeProduct($product_ids);
        }
        else{
            $product_ids = Mage::getSingleton('core/session')->getBarcodeProduct();
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
                                                ->addAttributeToSelect('sku')
                                                ->addAttributeToSelect('name')
                                                ->addAttributeToSelect('attribute_set_id')
                                                ->addAttributeToSelect('type_id')
                                                ->joinField('qty',
                                                    'cataloginventory/stock_item',
                                                    'qty',
                                                    'product_id=entity_id',
                                                    '{{table}}.stock_id=1',
                                                    'left');

        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('status');
        $collection->addAttributeToSelect('visibility');
        $collection->addFieldToFilter('entity_id',array('in'=> $product_ids));
        $collection->addFieldToFilter('type_id','simple');
            
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('barcode')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('barcode')->__('Name'),
                'index' => 'name',
        ));
        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('barcode')->__('Qty'),
                'width' => 50,
                'renderer'  => 'barcode/adminhtml_barcode_grid_renderer_qty',
        ));
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('barcode')->__('SKU'),
                'index' => 'sku'
        ));
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('barcode')->__('Type'),
                'width' => 100,
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('barcode')->__('Price'),
                'width' => 100,
                'index' => 'price',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
        ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('barcode')->__('Status'),
                'width' => 100,
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
					array(
                        'caption' => Mage::helper('catalog')->__('Print Barcode'),
                        'id' => "printbarcode",
                        'url'     => array(
                            'base'=>'adminhtml/barcode_print/oneproduct',
                            'params'=>array(
								'store'=>$this->getRequest()->getParam('store'),
								//'required' => $this->_getRequiredAttributesIds(),
								'popup'    => 1,
								//'product'  => $this->_getProduct()->getId()
							)
                        ),
						'onclick'  => 'window.open(this.href, "_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=800, height=600, left=20, top=20"); return false;',
                        'field'   => 'id',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return parent::_prepareColumns();
    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
} 
