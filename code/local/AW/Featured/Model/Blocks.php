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

class AW_Featured_Model_Blocks extends Mage_Core_Model_Abstract {
    public function _construct() {
        $this->_init('awfeatured/blocks');
    }

    public function _afterLoad() {
        if(is_string($this->getStore()))
            $this->setStore(@explode(',', $this->getStore()));
        if(is_string($this->getTypeData()))
            $this->setTypeData(@unserialize($this->getTypeData()));
        if(is_string($this->getAutomationData()))
            $this->setAutomationData(@unserialize($this->getAutomationData()));
    }

    public function _beforeSave() {
        if(is_array($this->getStore()))
            $this->setStore(@implode (',', $this->getStore()));
        if(is_array($this->getTypeData()))
            $this->setTypeData(@serialize($this->getTypeData()));
        if(is_array($this->getAutomationData()))
            $this->setAutomationData(@serialize($this->getAutomationData()));
    }

    public function loadByBlockId($blockId) {
        $this->load($blockId, 'block_id');
        return $this;
    }

    public function getRepresentation() {
        return $this->getType() ? Mage::getModel('awfeatured/representations_config')->getRepresentation($this->getType()) : null;
    }
}
