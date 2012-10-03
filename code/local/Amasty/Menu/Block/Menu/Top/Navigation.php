<?php 
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu_Top_Navigation extends Mage_Catalog_Block_Navigation
{
    /**
     * Get menu provider
     * @return Amasty_Menu_Block_Menu_Top
     */
    public function getMenuProvider()
    {
    	return Mage::getBlockSingleton('ammenu/menu_top');
    }
    
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
    	return $this->getMenuProvider()->renderCategoriesMenuHtml($level, $outermostItemClass, $childrenWrapClass);
    }
}