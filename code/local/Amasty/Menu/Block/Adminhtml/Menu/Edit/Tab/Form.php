<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Adminhtml_Menu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm ()
    {
        
        if (Mage::getSingleton('adminhtml/session')->getMenuData()) {
            $menuItemToEdit = Mage::getSingleton('adminhtml/session')->getMenuData();
        } elseif (Mage::registry('menu_data')) {
            $menuItemToEdit = Mage::registry('menu_data')->getData();
        }
        
        $form = new Varien_Data_Form();
        
        $this->setForm($form);
        $fieldset = $form->addFieldset(
        	'menu_form', 
            array(
            	'legend' => Mage::helper('ammenu')->__('Menu information')
            )
        );
        
        /* @var $menuModel Amasty_Menu_Model_Menu */
        $menuModel = Mage::getModel('ammenu/menu');

        $fieldset->addField('block_type', 'select', 
            array(
            	'label' => Mage::helper('ammenu')->__('Block to display'), 
        		'name' => 'block_type', 
            	'note' =>  Mage::helper('ammenu')->__('Please choose block to display menu item'),
        		'values' => Mage::helper('ammenu')->getBlockTypes()
            )
        );
        
        $stores = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
        
        $fieldset->addField('store_id', 'multiselect', 
            array(
            	'label' => Mage::helper('ammenu')->__('Store'), 
        		'name' => 'stores[]', 
        		'values' => $stores,
            	'required' => true, 
            	
            )
        );
        
        
        /**
         *	Get default pages tree
         */
        if (isset($menuItemToEdit['store_id'])) {
            $pages = $menuModel->getPagesTree($menuItemToEdit['store_id']);
        } else {
            $pages = $menuModel->getPagesTree();
        }
        
        
        $pagesToBind = array();
        
        foreach ($pages as $page) {
            $pagesToBind[] = array(
                'value' => $page->menu_id,
                'label' => str_repeat('--', $page->level) . Mage::helper('ammenu')->getMenuItemName($page)
            );
        }
        
        $fieldset->addField('parent_id', 'select', 
            array(
            	'label' => Mage::helper('ammenu')->__('Parent page'), 
        		'name' => 'parent_id', 
        		'values' => $pagesToBind
            )
        );
        
        $cmsPages = Mage::getModel('cms/page')->getCollection();
        
        if (isset($menuItemToEdit['store_id'])) {
            $cmsPages->addStoreFilter($menuItemToEdit['store_id']);
        }
        
        $cmsPages->load();
        
        $cmsPagesToBind = array();
        
        /* @var $cmsPage Mage_Cms_Model_Page */
        foreach ($cmsPages as $cmsPage) {
            $cmsPagesToBind[] = array(
                'value' => $cmsPage->getId(),
                'label' => $cmsPage->getTitle()
            );
        }
        
        $fieldset->addField('cms_page_id', 'select',
            array(
            	'label' => Mage::helper('ammenu')->__('Choose CMS Page or'), 
                'name' => 'cms_page_id',
                'values' => $cmsPagesToBind)
        );
        
        $fieldset->addField('url', 'text', 
            array(
				'label' => Mage::helper('ammenu')->__('Specify direct Url'), 
        		'name' => 'url'
            )
        );
        
        $fieldset->addField('name', 'text', 
            array(
            	'label' => Mage::helper('ammenu')->__('Name'), 
                'note' =>  Mage::helper('ammenu')->__('Leave it blank to use page title'),
        		'name' => 'name'
            )
        );
        
        $fieldset->addField('position', 'text', 
            array(
            	'label' => Mage::helper('ammenu')->__('Position'), 
        		'name' => 'position',
                'class' => 'validate-number',
            	'note' =>  Mage::helper('ammenu')->__('Specify order of menu item'),
            )
        );
        
        $fieldset->addField('status', 'select', 
            array(
            	'label' => Mage::helper('ammenu')->__('Status'), 
        		'name' => 'status', 
        		'values' => array(
                array('value' => 1, 'label' => Mage::helper('ammenu')->__('Enabled')), 
                array('value' => 2, 'label' => Mage::helper('ammenu')->__('Disabled')))
            )
        );
        
        if (Mage::getSingleton('adminhtml/session')->getMenuData()) {
            $form->setValues(
            Mage::getSingleton('adminhtml/session')->getMenuData());
            Mage::getSingleton('adminhtml/session')->setMenuData(null);
        } elseif (Mage::registry('menu_data')) {
            $form->setValues(Mage::registry('menu_data')->getData());
        }
        return parent::_prepareForm();
    }
}