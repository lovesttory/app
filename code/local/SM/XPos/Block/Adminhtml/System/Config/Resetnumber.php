<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 7/27/12
 * Time: 2:29 PM
 * To change this template use File | Settings | File Templates.
 */

class SM_XPos_Block_Adminhtml_System_Config_Resetnumber extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('sm/xpos/adminhtml/system/config/resetnumber.phtml');
        }
        return $this;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);

    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('customer')->__($originalData['button_label']),
            'html_id' => $element->getHtmlId(),
            'ajax_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/xPos/resetnumber'),
            'ajax_getlastorder_url' =>  Mage::getSingleton('adminhtml/url')->getUrl('*/xPos/getorderid')
        ));

        return $this->_toHtml();
    }

}