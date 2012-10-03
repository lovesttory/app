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


class AW_Featured_Block_Block extends Mage_Core_Block_Template {
    const REGISTRYSTORAGE_FILES = 'awafp_blocks_storage_files';
    const REGISTRYSTORAGE_FILES_ADDED = 'added';

    private $_block = null;
    private $_blocks = null;

    public function getBlockPosition() {
        switch($this->getNameInLayout()) {
            case 'awafp.sidebar.left.bottom':
                return AW_Featured_Model_Source_Autoposition::LEFTCOLUMN;
                break;
            case 'awafp.sidebar.right.bottom':
                return AW_Featured_Model_Source_Autoposition::RIGHTCOLUMN;
                break;
            case 'awafp.content.top':
                return AW_Featured_Model_Source_Autoposition::BEFORECONTENT;
                break;
            default:
                return AW_Featured_Model_Source_Autoposition::NONE;
        }
    }

    protected function _beforeToHtml() {
        if(!$this->getTemplate()) {
            if($this->getData('id') || $this->getData('increment_id'))
                $this->setTemplate('aw_featured/block.phtml');
            else
                $this->setTemplate('aw_featured/blocks.phtml');
        }
        return parent::_beforeToHtml();
    }

    public function getBlocks() {
        if(is_null($this->_block)) {
            if($this->getBlockPosition() != AW_Featured_Model_Source_Autoposition::NONE)
                $this->_blocks = Mage::getModel('awfeatured/blocks')->getCollection()
                    ->setPositionFilter($this->getBlockPosition())
                    ->setStoreFilter(Mage::app()->getStore()->getId())
                    ->setEnabledFilter();
        }
        return $this->_blocks;
    }

    public function getBlock() {
        if(is_null($this->_block)) {
            if($this->getData('id'))
                $this->_block = Mage::getModel('awfeatured/blocks')->loadByBlockId($this->getData('id'));
            if($this->getData('increment_id'))
                $this->_block = Mage::getModel('awfeatured/blocks')->load($this->getData('increment_id'));
            if($this->_block->getData() == array())
                $this->_block = null;
        }
        return $this->_block;
    }

    public function getHtmlCode($block = null) {
        if(is_null($block))
            $block = $this->getBlock();
        $block->afterLoad();
        if($block && $block->getRepresentation()
            && $block->getRepresentation()->getBlock()
            && $blockObj = $this->getLayout()->createBlock($block->getRepresentation()->getBlock())) {
            $blockObj->setAFPBlock($block);
            return $blockObj->toHtml();
        } else {
            return null;
        }
        return $block->toHtml();
    }

    protected function _getRegistryStorage() {
        $_storage = Mage::registry(self::REGISTRYSTORAGE_FILES);
        if(!$_storage) {
            $_storage = new Varien_Object();
            Mage::register(self::REGISTRYSTORAGE_FILES, $_storage);
        }
        return $_storage;
    }

    protected function _canAddFile($file) {
        $_storage = $this->_getRegistryStorage();
        return is_null($_storage->getData(base64_encode($file)));
    }

   /*
    protected function _addCss($file) {
        $_storage = $this->_getRegistryStorage();
        $_storage->setData(base64_encode($file), self::REGISTRYSTORAGE_FILES_ADDED);
        return '<link media="all" rel="stylesheet" type="text/css" href="'.$this->getSkinUrl($file).'" />';
    }
   */

    protected function _addJs($file) {
        $_storage = $this->_getRegistryStorage();
        $_storage->setData(base64_encode($file), self::REGISTRYSTORAGE_FILES_ADDED);
        return '<script type="text/javascript" src="'.$this->getSkinUrl($file).'"></script>';
    }

    public function getCssJsIncludes($block = null) {
        if(is_null($block))
            $block = $this->getBlock();
        $includes = array();
        if($block && $block->getRepresentation()) {
            /*if($block->getRepresentation()->getCss()) {
                $_cssFiles = @explode(',', $block->getRepresentation()->getCss());
                foreach($_cssFiles as $_cssFile)
                    if($this->_canAddFile($_cssFile))
                        $includes[] = $this->_addCss($_cssFile);
            }*/
            if($block->getRepresentation()->getJs()) {
                $_jsFiles = @explode(',', $block->getRepresentation()->getJs());
                foreach($_jsFiles as $_jsFile)
                    if($this->_canAddFile($_jsFile))
                        $includes[] = $this->_addJs($_jsFile);
            }
        }
        $includes = @implode("\n", $includes);
        return $includes;
    }
}
