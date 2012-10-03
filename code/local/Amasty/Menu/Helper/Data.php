<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Helper_Data extends Mage_Core_Helper_Abstract
{
	const PLACEMENT_TOP	= 1;
    const PLACEMENT_SIDEBAR	= 2;
    const PLACEMENT_BOTTOM	= 3;
    
    public function renderMenuItem($menuItem)
    {
        /* @var $menuItem Amasty_Menu_Model_Menu */
        $url = $this->getMenuItemUrl($menuItem);
        $name = $this->getMenuItemName($menuItem);
        
        return sprintf('<a href="%s"><span>%s</span></a>', $url, $name);
    }
    
    public function getMenuItemUrl($menuItem)
    {
        if (!empty($menuItem->url)) {
            return $menuItem->url;
        } else {
            /* @var $page Mage_Cms_Model_Page */            
            return Mage::Helper('cms/page')->getPageUrl($menuItem->cms_page_id);
        }
    }
    
    public function getMenuItemName($menuItem)
    {
        if (!empty($menuItem->name)) {
            return $menuItem->name;
        } else {
            /* @var $page Mage_Cms_Model_Page */            
            $page = Mage::getSingleton('cms/page');
            $obj = $page->load($menuItem->cms_page_id);
            return $obj->getTitle();
        }
    }
	
    /**
     * Get possible menu placement
     * @return array 
     */
    public function getBlockTypes()
    {
    	return array(
                    self::PLACEMENT_TOP => Mage::helper('ammenu')->__('Top'), 
                    self::PLACEMENT_SIDEBAR => Mage::helper('ammenu')->__('Sidebar'),
                    self::PLACEMENT_BOTTOM => Mage::helper('ammenu')->__('Bottom')
		);
    }
}