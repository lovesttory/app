<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Adminhtml_Menu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('menu_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ammenu')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ammenu')->__('Item Information'),
          'title'     => Mage::helper('ammenu')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('ammenu/adminhtml_menu_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}