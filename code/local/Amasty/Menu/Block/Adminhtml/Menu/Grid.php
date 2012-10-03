<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Adminhtml_Menu_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct ()
    {
        parent::__construct();
        $this->setId('menuGrid');
        $this->setDefaultSort('menu_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection ()
    {
        $collection = Mage::getModel('ammenu/menu')->getCollection();
        
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    protected function _prepareColumns ()
    {
        $this->addColumn('menu_id', 
            array(
				'header' => Mage::helper('ammenu')->__('ID'), 
				'align' => 'right', 
        		'width' => '50px',
            	'index' => 'menu_id'
            )
        );
        
        $this->addColumn('block_type', 
            array(
            	'header' => Mage::helper('ammenu')->__('Block to display'), 
            	'align' => 'left', 
        		'width' => '80px', 
        		'index' => 'block_type', 
        		'type' => 'options', 
        		'options' => Mage::helper('ammenu')->getBlockTypes()
            )
        );
        
        $this->addColumn('title', 
            array(
            	'header' => Mage::helper('ammenu')->__('Title'), 
            	'align' => 'left', 
        		'index' => 'name'
            )
        );
        if (! Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
            	'store_id', 
                array(
                	'header' => Mage::helper('ammenu')->__('Store View'), 
                    'index' => 'store_id', 
                    'type' => 'store', 
                    'store_all' => true, 
                    'store_view' => true, 
                    'sortable' => false, 
                    'filter_condition_callback' => array($this, '_filterStoreCondition')));
        }
        
        $this->addColumn('status', 
            array(
            	'header' => Mage::helper('ammenu')->__('Status'), 
            	'align' => 'left', 
        		'width' => '80px', 
        		'index' => 'status', 
        		'type' => 'options', 
        		'options' => 
                array(
                    1 => Mage::helper('ammenu')->__('Enabled'), 
                    2 => Mage::helper('ammenu')->__('Disabled')
                )
            )
        );
        
        $this->addColumn('position', 
            array(
            	'header' => Mage::helper('ammenu')->__('Position'), 
            	'align' => 'left', 
        		'width' => '80px', 
        		'index' => 'position', 
            )
        );
        
        $this->addColumn('action', 
            array(
            	'header' => Mage::helper('ammenu')->__('Action'), 
            	'width' => '100', 
                'type' => 'action', 
                'getter' => 'getId', 
                'actions' => array(
                    array(
                    	'caption' => Mage::helper('ammenu')->__('Edit'), 
        				'url' => array('base' => '*/*/edit'), 
        				'field' => 'id'
                    )
                ), 
    			'filter' => false, 
    			'sortable' => false, 
    			'index' => 'stores', 
    			'is_system' => true
            )
        );
        
        $this->addExportType('*/*/exportCsv', Mage::helper('ammenu')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('ammenu')->__('XML'));
        
        return parent::_prepareColumns();
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value, false);
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    protected function _prepareMassaction ()
    {
        $this->setMassactionIdField('menu_id');
        $this->getMassactionBlock()->setFormFieldName('menu');
        
        $this->getMassactionBlock()->addItem('delete', 
            array(
            	'label' => Mage::helper('ammenu')->__('Delete'), 
        		'url' => $this->getUrl('*/*/massDelete'), 
            	'confirm' => Mage::helper('ammenu')->__('Are you sure?')));
            
        $statuses = Mage::getSingleton('ammenu/status')->getOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));
        
        $this->getMassactionBlock()->addItem('status', 
            array(
            	'label' => Mage::helper('ammenu')->__('Change status'), 
        		'url' => $this->getUrl('*/*/massStatus', array('_current' => true)), 
        		'additional' => array(
        			'visibility' => array(
        				'name' => 'status', 'type' => 'select', 
        				'class' => 'required-entry', 
           				'label' => Mage::helper('ammenu')->__('Status'), 'values' => $statuses))));
        return $this;
    }
    public function getRowUrl ($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}