<?php 
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu_Top_Menu extends Mage_Page_Block_Html_Topmenu
{
	const MENU_LEVEL = 0;
	/**
     * Get menu provider
     * @return Amasty_Menu_Block_Menu_Top
     */
    public function getMenuProvider()
    {
    	return Mage::getBlockSingleton('ammenu/menu_top');
    }
    
    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {
    	return $this->getMenuProvider()->renderCategoriesMenuHtml(self::MENU_LEVEL, $outermostClass, $childrenWrapClass);
    }
}