<?php 

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu_Sidebar extends Amasty_Menu_Block_Menu
{
    public function _prepareLayout()
    {
        $this->_settingGroup = 'sidebarmenu';
		return parent::_prepareLayout();
    }
    
    public function getSidebarMenu()
    {
    	if (!$this->shouldDisplay()) {
    		return array();
    	}
    	
        $parentPageId = 0; 
        
        /* @var $menuModel Amasty_Menu_Model_Menu */
        $menuModel = Mage::getModel('ammenu/menu');
        $storeId = Mage::app()->getStore()->getId();
        
        $pages = $menuModel->getPagesTree(array($storeId), $parentPageId, Amasty_Menu_Model_Status::STATUS_ENABLED, 0, Amasty_Menu_Helper_Data::PLACEMENT_SIDEBAR);
        
        $currentPage = $this->getCurrentPage();
        
		$currentPageId = $currentPage->getId();
		
		$isHomePage = $this->isHomePage();
		
		
		$limitLevel = $this->getBlockParameter('level');
		$spacerSymbol = $this->getBlockParameter('spacer');
		
		$pathIds = $this->getPathToPage($currentPageId, true);
		
		if (!$pathIds) {
			$pathIds = array();
		}
		
		$parentId = 0;
		
		$menu = array();
		
		foreach ($pages as $i => $menuItem) {
			/* Display only levels above 0 */
		    if ($menuItem->level == 0) {
		        continue;
		    }
		    
		    $currentClass = '';
		    
		    $menuItem->classes = array();
		    
		    if ($menuItem->cms_page_id == $currentPageId) {
		        $menuItem->classes[] = 'current';
		    }
		    
		    if ($isHomePage) {
		        if (in_array($menuItem->menu_id, $pathIds) || 
		            $menuItem->level == 1 || 
		            $menuItem->level <= $limitLevel) 
		        {
		        	$menuItem->label = str_repeat($spacerSymbol, $menuItem->level) . Mage::helper('ammenu')->renderMenuItem($menuItem);
		        }
		    } else {
		        if (in_array($menuItem->menu_id, $pathIds) || 
		            $menuItem->level == 1 || 
		            in_array($menuItem->parent_id, $pathIds)) 
		        {
	    	        $menuItem->label = str_repeat($spacerSymbol, $menuItem->level) . Mage::helper('ammenu')->renderMenuItem($menuItem);    
		        }  
		    }
		    $parentId = $menuItem->parent_id;
		    
		    $menu[] = $menuItem;
		}
        return $menu;            	
    }
    
    /**
     * Check of displaying block based on settings and current layout
     * @return boolean
     */
    public function shouldDisplay()
    {
    	if ($this->placedInLayout()) {
    		return ($this->getBlockParameter('wheretoshow') === $this->getNameInLayout());
    	} else {
    		return true;
    	}    	
    }
    
    /**
     * Check that menu was placed in layout, not in cms page
     * @return boolean
     */
    public function placedInLayout()
    {
    	$places = array(
    		"menu_left",
    		"menu_right"
    	);
    	return in_array($this->getNameInLayout(), $places);
    }
}