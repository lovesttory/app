<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Featured
 * @version    3.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Featured_Block_Adminhtml_Blocks_Edit_Tab_Automation extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $_form = new Varien_Data_Form();
        $this->setForm($_form);
        $_data = Mage::getSingleton('adminhtml/session')->getData(AW_Featured_Helper_Data::FORM_DATA_KEY);
        if(!is_object($_data))
            $_data = new Varien_Object($_data);

        $_dataF = array('automation_type' => $_data->getAutomationType());
        if($_data->getAutomationData()) {
            foreach($_data->getAutomationData() as $key => $value) {
                if($key == 'products') $_dataF['automation_data_products'] = $value;
                $_dataF[$key] = $value;
            }
        }

        $_fieldset = $_form->addFieldset('automation_settings', array(
            'legend' => $this->__('Automation Settings')
        ));

        $_fieldset->addField('automation_type', 'select', array(
            'name' => 'automation_type',
            'label' => $this->__('Automation Type'),
            'values' => Mage::getModel('awfeatured/source_automation')->toOptionArray()
        ));

        $_fieldset->addField('gridcontainer_products', 'note', array(
            'label' => $this->__('Select Products'),
            'text' => Mage::getSingleton('core/layout')->createBlock('awfeatured/adminhtml_blocks_edit_tab_automation_productsgrid')->toHtml()
        ));

        $_fieldset->addField('automation_data_products', 'hidden', array(
            'name' => 'automation_data[products]'
        ));

        $_fieldset->addField('gridcontainer_categories', 'note', array(
            'label' => $this->__('Select Categories'),
            'text' => Mage::getSingleton('core/layout')->createBlock('awfeatured/adminhtml_blocks_edit_tab_automation_categoriesgrid')->toHtml()
        ));

        $_fieldset->addField('automation_data_categories', 'hidden', array(
            'name' => 'automation_data[categories]'
        ));
        
        $_fieldset->addField('current_category_type', 'select', array(
            'name' => 'current_category_type',
            'label' => $this->__('Current Category Automation Type'),
            'values' => Mage::getModel('awfeatured/source_automation_currentcategory')->toOptionArray()
        ));

        $_ql = $_fieldset->addField('quantity_limit', 'text', array(
            'name' => 'automation_data[quantity_limit]',
            'label' => $this->__('Quantity Limit'),
            'required' => true
        ));

        $_form->setValues($_dataF);
    }
}
