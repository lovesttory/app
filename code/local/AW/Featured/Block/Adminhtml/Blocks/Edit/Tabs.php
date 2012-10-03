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

class AW_Featured_Block_Adminhtml_Blocks_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct() {
        parent::__construct();
        $this->setId('awfeatured_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Block Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('general', array(
            'label' => $this->__('General'),
            'title' => $this->__('General'),
            'content' => $this->getLayout()->createBlock('awfeatured/adminhtml_blocks_edit_tab_general')->toHtml()
        ));

        $this->addTab('automation', array(
            'label' => $this->__('Automation Settings'),
            'title' => $this->__('Automation Settings'),
            'content' => $this->getLayout()->createBlock('awfeatured/adminhtml_blocks_edit_tab_automation')->toHtml()
        ));

        if($this->getRequest()->getParam('continue_tab'))
            $this->setActiveTab($this->getRequest()->getParam('continue_tab'));

        return parent::_beforeToHtml();
    }
}
