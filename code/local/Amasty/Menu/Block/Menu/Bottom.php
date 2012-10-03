<?php 
class Amasty_Menu_Block_Menu_Bottom extends Amasty_Menu_Block_Menu
{
    public function _prepareLayout()
    {
        $this->_settingGroup = 'bottommenu';
		return parent::_prepareLayout();
    }
    
    /**
     * Get bottom menu
     * @return array
     */
    public function getBottomMenu()
    {
    	$pages = array();
    	
        $parentPageId = $this->getBlockParameter('parent');

        if (!$this->getBlockParameter('display')) {
        	return $pages;
        }
        
        if ($parentPageId) {
            /* @var $menuModel Amasty_Menu_Model_Menu */
            $menuModel = Mage::getModel('ammenu/menu');
            $storeId = Mage::app()->getStore()->getId();
            
            /*
             * Get enabled pages only
             */
            $pages = $menuModel->getPagesTree(array($storeId), $parentPageId, Amasty_Menu_Model_Status::STATUS_ENABLED, 0, Amasty_Menu_Helper_Data::PLACEMENT_BOTTOM);
        }
        
        
        $page = $this->getCurrentPage();
        $parentLevel = 0;
        $menu = array();
        
	    foreach ($pages as $menuItem) {
	    	
		    $activeClass = '';
		    $menuItem->classes = array();
		    
		    if ($page->getId() == $menuItem->cms_page_id) {
		    	$menuItem->classes[] = 'current';
		    }
		    
		    if ($menuItem->level == $parentLevel) {
		        if (isset($level)) {
		        	$menuItem->closeTag = '</ul>';
		        } else {
		        	$menuItem->closeTag = '';
		        }
		        
		        $menuItem->openTag = '<ul>';
		        $menuItem->classes[] = 'parent';
		        $menuItem->label = Mage::helper('ammenu')->renderMenuItem($menuItem);
		        
		    } else {
		        $menuItem->label = Mage::helper('ammenu')->renderMenuItem($menuItem);
		    }
		    $level = $menuItem->level;
		    
		    $menu[] = $menuItem;
		}
        return $menu;
    }
}