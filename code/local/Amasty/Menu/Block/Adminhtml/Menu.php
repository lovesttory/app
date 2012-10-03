<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_menu';
    $this->_blockGroup = 'ammenu';
    $this->_headerText = Mage::helper('ammenu')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('ammenu')->__('Add Item');
    parent::__construct();
  }
}