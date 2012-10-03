<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Menu_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get menu block
     * @return Amasty_Menu_Block_Menu
     */
    public function getMenuBlock()
    {
    	return Mage::getBlockSingleton('ammenu/menu');
    }

    protected function _toHtml()
    {
        if (is_array($this->_crumbs)) {
            reset($this->_crumbs);
            $this->_crumbs[key($this->_crumbs)]['first'] = true;
            end($this->_crumbs);
            $this->_crumbs[key($this->_crumbs)]['last'] = true;
        }
        
        if ($page = $this->getMenuBlock()->getCurrentPage()) {
            
            $path = $this->getMenuBlock()->getPathToPage($page->getId());
            
            /*
             * Page belongs to menu - display it
             */
            if (count($path)) {
            
             	/* @var $helper Amasty_Menu_Helper_Data */
                $helper = Mage::helper('ammenu');
                
                $length = count($this->_crumbs);
                while($length > 1) {
                    array_pop($this->_crumbs);
                    $length--;
                }
                
                $title = array();
                
                foreach ($path as $menuItem) {
                    
                    $naming = $helper->getMenuItemName($menuItem);
                    $link = $helper->getMenuItemUrl($menuItem);
                    
                    $crumb = array(
                        'label' => $naming, 
                        'title' => $naming, 
                        'link' => $link,
                        'last' =>  null
                    );
                    
                    if ($menuItem->cms_page_id === $page->getId()) {
                        $crumb['link'] = null;
                        $crumb['last'] = true;
                    }
                    $this->_crumbs[] = $crumb;
                    
                    $title[] = $naming;
                }
                
	            if ($headBlock = $this->getLayout()->getBlock('head')) {
	                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
	            }
            }
        }

        $this->assign('crumbs', $this->_crumbs);
        return parent::_toHtml();
    }
}
