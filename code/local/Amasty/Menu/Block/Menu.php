<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu extends Mage_Core_Block_Template
{
    /**
	 * Holds block configuration
	 * @var array
	 */
	protected $_blockData;
	
	/**
	 * Group name for menu setting
	 * @var string
	 */
	protected $_settingGroup;
    
    public function _prepareLayout()
    {
        /*
    	 * Set block configuration
    	 */
    	$this->_blockData = $this->getData();
    	
    	return parent::_prepareLayout();
    }
    
 	/**
     * Get block parameter
     * If not defined - get it from configuration for module
     * @param string $param
     * @return NULL
     */
    public function getBlockParameter($param)
    {
    	if (isset($this->_blockData[$param])) {
    		return $this->_blockData[$param];
    	} else {
    	    return Mage::getStoreConfig('ammenu/' . $this->_settingGroup . '/' . $param);    		
    	}
    }
    
    /**
     * Get path to menu
     * @param int $menuId
     * @param bool $returnIdentifiersOnly
     * @return array
     */
    public function getPathToMenu($menuId, $returnIdentifiersOnly = false)
    {
        if (!$menuId) {
            return;
        }
        /* @var $menuModel Amasty_Menu_Model_Menu */
        $menuModel = Mage::getModel('ammenu/menu');
        $path = $menuModel->getPathToMenu($menuId);
        
        if ($returnIdentifiersOnly) {
            $identifiers = array();
            foreach ($path as $pathItem) {
                $identifiers[] = $pathItem->menu_id;
            }
            return $identifiers;
        } 
        return $path;
    }
    
    /**
     * Get path to page
     * @param int $pageId
     * @param bool $returnIdentifiersOnly
     * @return array
     */
    public function getPathToPage($pageId, $returnIdentifiersOnly = false)
    {
        if (!$pageId) {
            return;
        }
        /* @var $menuModel Amasty_Menu_Model_Menu */
        $menuModel = Mage::getModel('ammenu/menu');
        
        $menuId = $menuModel->getMenuIdByPageId($pageId);
        
        return $this->getPathToMenu($menuId, $returnIdentifiersOnly);
    }
    
    
    /**
     * Is current page home or not
     * @return boolean
     */
    public function isHomePage()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page == 'cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }
        return $homePage;
    }
    
    
    /**
     * Get current page
     * @return Mage_Cms_Model_Page
     */
    public function getCurrentPage()
    {    
         /* @var $page Mage_Cms_Model_Page */
        $page = Mage::getSingleton('cms/page');
        return $page;
    }
}