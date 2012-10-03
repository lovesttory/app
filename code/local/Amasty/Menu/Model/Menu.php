<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Menu_Model_Menu extends Mage_Core_Model_Abstract
{
	
    const ALL_STORES_ID = 0;
    
	private $menuItems = array();
	
	private $menuPath = array();
	
	private $level = 0;
	
	/**
	 * Holds table name for menu items
	 * @var string
	 */
	private $menuTableName;
	
	/**
	 * Holds table name for relation between menu store
	 * @var unknown_type
	 */
	private $menuStoreTableName;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('ammenu/menu');
        
        $this->menuTableName = Mage::getSingleton('core/resource')->getTableName('ammenu/menu');        
        $this->menuStoreTableName = Mage::getSingleton('core/resource')->getTableName('ammenu/menu_store');
    }
    
    public function save()
    {
        parent::save();
    }
    
    public function getMenuIdByPageId($pageId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
    	
    	$sql = sprintf('SELECT menu_id FROM %s as menu ' .
    		   ' WHERE menu.cms_page_id = %d', $this->menuTableName, $pageId);

    	$data = $connection->fetchAll($sql);
    	
    	if (count($data)) {
    	    return $data[0]['menu_id'];
    	}
    }
    
    /**
     * Get path to menu
     * @param int $menuId
     * @param int $status
     * @return array
     */
    public function getPathToMenu($menuId, $status = 1)
    {
    	$connection = Mage::getSingleton('core/resource')->getConnection('core_read');

    	$sql = sprintf('SELECT * FROM %s as menu  ' .
    		   ' WHERE menu.menu_id = %d', 
    		$this->menuTableName, $menuId);
        
        
    	$data = $connection->fetchAll($sql);
		
		if (count($data)) {
			foreach ($data as $item) {   
			    $obj = (object)$item;
			    if ($obj->parent_id > 0) {
				    $this->menuPath[] = $obj;
				    $this->getPathToMenu($obj->parent_id, $status);
			    }
			}
		}
		return array_reverse($this->menuPath);
    }
    
    public function getPageUrl($pageId)
    {
        $page = Mage::getSingleton('cms/page');
        $pageObject = $page->load($pageId);
        return Mage::Helper('cms/page')->getPageUrl($menuItem->cms_page_id);
    }
    
    /**
     * Get pages tree
     * @return array
     */
    public function getPagesTree($storeIds = array(0), $startingFrom = 0,  $status = null, $level = 0, $block = 0)
    {
        if (array_search(self::ALL_STORES_ID, $storeIds) === false) {
            $storeIds[] = self::ALL_STORES_ID;
        }
        
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        
        $sql = sprintf('SELECT * FROM %s menu ' .
    		' right join %s store ON store.menu_id = menu.menu_id ' .
    		' WHERE menu.parent_id = %d ' .
    		' and store.store_id in (' . implode(",", $storeIds) . ')', $this->menuTableName, $this->menuStoreTableName, $startingFrom); 
        
    	if ($status) {
    	    $sql .= $connection->quoteInto(' and menu.status = ?', $status);
    	}
    	
    	if ($block == Amasty_Menu_Helper_Data::PLACEMENT_BOTTOM) {
    		$sql .= $connection->quoteInto(' and menu.block_type = ?', $block);
    	} else {
    		 if ($block && $level > 0) {
    		 	$sql .= $connection->quoteInto(' and menu.block_type = ?', $block);
    		 }
    	}
    	
        $sql .= ' group by menu.menu_id order by menu.position ASC';
        
        
    	$data = $connection->fetchAll($sql);
		
		if (count($data)) {
			foreach ($data as $item) {   
			    $obj = (object)$item;
			    $obj->level = $level; 	
				$this->menuItems[] = $obj;
				
				$this->getPagesTree($storeIds, $obj->menu_id, $status, $level + 1, $block);
			}
		}
		return $this->menuItems;
    }
}