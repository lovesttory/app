<?php 
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu_Top extends Mage_Catalog_Block_Navigation
{
    const DISPLAY_BEFORE_CATEGORIES = 0;
    const DISPLAY_AFTER_CATEGORIES = 1;
    const DISPLAY_INSTEAD_CATEGORIES = 2;
    const DONT_DISPLAY = 3;
    
	 /**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $html = '';
        $displayOption = $this->getDisplayOption();
        
                
        /* 
         * Get categories only if menu not replaces categories         
         */
        if ($displayOption != self::DISPLAY_INSTEAD_CATEGORIES) {
            $activeCategories = array();
            foreach ($this->getStoreCategories() as $child) {
                if ($child->getIsActive()) {
                    $activeCategories[] = $child;
                }
            }
            $activeCategoriesCount = count($activeCategories);
            $hasActiveCategoriesCount = ($activeCategoriesCount > 0);
    
            if ($hasActiveCategoriesCount) {
	            $j = 0;
	            foreach ($activeCategories as $category) {
	                $html .= $this->_renderCategoryMenuItemHtml(
	                    $category,
	                    $level,
	                    ($j == $activeCategoriesCount - 1),
	                    ($j == 0),
	                    true,
	                    $outermostItemClass,
	                    $childrenWrapClass,
	                    true
	                );
	                $j++;
	            }
            }
        }
        if ($displayOption != self::DONT_DISPLAY) {
        
            $storeId = Mage::app()->getStore()->getId();
            
             /* @var $menuModel Amasty_Menu_Model_Menu */
            $menuModel = Mage::getModel('ammenu/menu');	
            
            $pagesTree = $menuModel->getPagesTree(array($storeId), 0, Amasty_Menu_Model_Status::STATUS_ENABLED, 0, Amasty_Menu_Helper_Data::PLACEMENT_TOP);
            
            
            $menuHtml = '';
            
            $initialLevel = 1;
            $previousLevel = 1;
            
            foreach ($pagesTree as $page) {
                if ($page->level == 0) {
                    continue;
                }
                
            	if ($page->level < $previousLevel) {
                	$menuHtml .= str_repeat('</li></ul>', abs($previousLevel - $page->level));
                }
                
                $menuHtml .= $this->renderMenu($page, $previousLevel);
                
                $previousLevel = $page->level;
                
            }
            
            $menuHtml .= str_repeat('</li></ul>', abs($previousLevel - $initialLevel));
            
            if ($displayOption == self::DISPLAY_AFTER_CATEGORIES) {
                $html .= $menuHtml;
            }
            
            if ($displayOption == self::DISPLAY_BEFORE_CATEGORIES) {
                $html = $menuHtml . $html;
            }
            
            if ($displayOption == self::DISPLAY_INSTEAD_CATEGORIES) {
                $html = $menuHtml;
            }
        }
        
        
        return $html;
    }
	
    public function renderMenu($menuItem, $previousLevel) 
    {
    	$html = '';
    	
        $level = $menuItem->level;
        if ($level > $previousLevel) {
            $html .= '<ul class="level">';
            $html .= '<li>' . Mage::helper('ammenu')->renderMenuItem($menuItem);
				
        } 
        
        if ($level == $previousLevel) {
            $html .= '</li><li>' . Mage::helper('ammenu')->renderMenuItem($menuItem);
        }
        
        if ($level < $previousLevel) {
            $html .= '<li>' 
            	  . Mage::helper('ammenu')->renderMenuItem($menuItem);
        }     
                
        return $html;
    }
    
    public function getDisplayOption()
    {
        return Mage::getStoreConfig('ammenu/topmenu/wheretoshow');
    }
}