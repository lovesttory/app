<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Block_Adminhtml_Menu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ammenu';
        $this->_controller = 'adminhtml_menu';
        
        $this->_updateButton('save', 'label', Mage::helper('ammenu')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ammenu')->__('Delete Item'));
		
        $formKey = Mage::getSingleton('core/session')->getFormKey();

        $this->_formScripts[] = "
            function updateParentPages()
            {
	            
            	new Ajax.Request('" . $this->getUrl('*/*/menutree', array('isAjax'=>true)) ."?' + $('store_id').serialize(), {
	                parameters: {
    				},
	                onSuccess: function(transport) {
	                    $('parent_id').update(transport.responseText);
	                }
	            });
	            
	            new Ajax.Request('" . $this->getUrl('*/*/pages', array('isAjax'=>true)) ."?' + $('store_id').serialize(), {
	                parameters: {
	                	form_key : '" . $formKey . "'
    				},
	                onSuccess: function(transport) {
	                    $('cms_page_id').update(transport.responseText);
	                }
	            });
            	           
            }
            obj = document.getElementById('store_id');
            Event.observe(obj,'change', updateParentPages);
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('menu_data') && Mage::registry('menu_data')->getId() ) {
            return Mage::helper('ammenu')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('menu_data')->getName()));
        } else {
            return Mage::helper('ammenu')->__('Add Item');
        }
    }
}