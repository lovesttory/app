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

class AW_Featured_Block_Adminhtml_Blocks_Edit_Tab_Automation_Products_Images extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('awfeatured_primselector');
        $this->setDefaultSort('position');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
    }
    
    protected function _prepareCollection() {
        $_pid = $this->getRequest()->getParam('pid');
        $_collection = Mage::helper('awfeatured/images')->getGalleryImages($_pid);
        $this->setCollection($_collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->unsetChild('reset_filter_button');
        $this->unsetChild('search_button');
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => $this->__('Use Default Image'),
                    'onclick'   => 'awfBForm.setProductImage('.$this->getRequest()->getParam('pid').', '.$this->getRequest()->getParam('blockId').', -1);',
                    'class'   => 'save'
                ))
        );
        return $this;
    }
    
    protected function _prepareColumns() {
        $this->addColumn('image', array(
            'header'    => $this->__('Image'),
            'width'     => '150px',
            'index'     => 'value_id',
            'renderer' => 'AW_Featured_Block_Widget_Grid_Column_Renderer_Galleryimage',
            'sortable' => false,
            'filter' => false
        ));
        
        $this->addColumn('title', array(
            'header' => $this->__('Title'),
            'index' => 'label',
            'sortable' => false,
            'filter' => false
        ));
        
        $this->addColumn('position', array(
            'header' => $this->__('Position'),
            'index' => 'position',
            'filter' => false,
            'sortable' => false,
            'width' => '100px'
        ));
    }
    
    public function getGridUrl() {
        return $this->getUrl('*/*/getisform', array('_current'=>true, 'pid' => $this->getRequest()->getParam('pid'),  'blockId' => $this->getRequest()->getParam('blockId')));
    }
    
    public function getRowUrl($item) {
        return 'javascript:awfBForm.setProductImage('.$this->getRequest()->getParam('pid').', '.$this->getRequest()->getParam('blockId').', '.$item->getData('value_id').');';
    }
}
