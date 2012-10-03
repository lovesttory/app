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


class AW_Featured_Block_Representations_Common extends Mage_Core_Block_Template {
    private $_awafpblock = null;
    private $_uniqueBlockId = null;
    private $_collection = null;

    public function setAFPBlock($block) {
        $this->_awafpblock = $block;
        return $this;
    }

    protected function _getShowOutOfStock() {
        $_show = true;
        if(($_ciHelper = Mage::helper('cataloginventory')) && method_exists($_ciHelper, 'isShowOutOfStock')) {
            $_show = $_ciHelper->isShowOutOfStock();
        }
        return $_show;
    }

    protected function _prepareCollection($_collection) {
        $_visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        $_collection->addAttributeToFilter('visibility', $_visibility)
            ->addAttributeToFilter('status',array("in"=>Mage::getSingleton("catalog/product_status")->getVisibleStatusIds()));
        if(!$this->_getShowOutOfStock())
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_collection);
        $_collection->addUrlRewrites()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->groupByAttribute('entity_id');
        return $_collection;
    }

    protected function _addCategoriesFilter($collection) {
        $_automationData = $this->getAFPBlock()->getAutomationData();
        $_categories = isset($_automationData['categories']) ? @explode(',', $_automationData['categories']) : array();
        $_categories = array_filter($_categories, array(Mage::helper('awfeatured'), 'removeEmptyItems'));
        if(!$_categories) {
            $this->setIsEmpty(true);
        } else {
            $collection->addCategoriesFilter($_categories);
        }
        return $collection;
    }

    protected function _getCollectionForIds() {
        $_collection = $this->_prepareCollection(Mage::getModel('awfeatured/product_collection'));
        if($this->getAFPBlock()->getAutomationData()) {
            $_automationData = $this->getAFPBlock()->getAutomationData();
            $_products = isset($_automationData['products']) ? @explode(',', $_automationData['products']) : array();
            $_products = array_filter($_products, array(Mage::helper('awfeatured'), 'removeEmptyItems'));
            if(!$_products) {
                $this->setIsEmpty(true);
            } else {
                $_collection->addAttributeToFilter('entity_id', $_products);
            }
            $_collection->getSelect()->joinLeft(array('pi' => $_collection->getTable('awfeatured/productimages')),
                '(pi.product_id = e.entity_id) AND (pi.block_id = '.$this->getAFPBlock()->getData('id').')',
                array('image_id')
            );
        }
        return $_collection;
    }

    protected function _getTopSellersCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if($collection === null)
            $this->_addCategoriesFilter($_collection);
        $_collection->addOrderedQty()
                ->sortByOrderedQty();
        $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _getRandomProductsCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if($collection === null)
            $this->_addCategoriesFilter($_collection);
        $_collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        if($collection === null)
            $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _getRecentlyAddedCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if($collection === null)
            $this->_addCategoriesFilter($_collection);
        $_collection->addAttributeToSort('created_at','desc');
        if($collection === null)
            $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _getTopRatedCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if($collection === null)
            $this->_addCategoriesFilter($_collection);
        $_collection->joinOveralRating();
        $_collection->sortByRating();
        if($collection === null)
            $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _getMostReviewedCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if($collection === null)
            $this->_addCategoriesFilter($_collection);
        $_collection->joinReviewsCount();
        $_collection->sortByReviewsCount();
        if($collection === null)
            $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _getCurrentCategoryCollection($collection = null) {
        $_collection = $collection === null ? $this->_prepareCollection(Mage::getModel('awfeatured/product_collection')) : $collection;
        if(Mage::registry('current_category') && Mage::registry('current_category')->getId()) {
            $_collection->addCategoriesFilter(Mage::registry('current_category')->getId(), true);
            switch($this->getAFPBlockAutomationData('current_category_type')) {
                case AW_Featured_Model_Source_Automation_Currentcategory::RANDOM:
                    $this->_getRandomProductsCollection($_collection);
                    break;
                case AW_Featured_Model_Source_Automation_Currentcategory::TOPSELLERS:
                    $this->_getTopSellersCollection($_collection);
                    break;
                case AW_Featured_Model_Source_Automation_Currentcategory::TOPRATED:
                    $this->_getTopRatedCollection($_collection);
                    break;
                case AW_Featured_Model_Source_Automation_Currentcategory::MOSTREVIEWED:
                    $this->_getMostReviewedCollection($_collection);
                    break;
                case AW_Featured_Model_Source_Automation_Currentcategory::RECENTLYADDED:
                default:
                    $this->_getRecentlyAddedCollection($_collection);
                    break;
            }
        } else {
            $this->setIsEmpty(true);
        }
        if($collection === null)
            $this->_postprocessCollection($_collection);
        return $_collection;
    }

    protected function _postprocessCollection($collection) {
        $_automationData = $this->getAFPBlock()->getAutomationData();
        $_pSize = isset($_automationData['quantity_limit']) ? $_automationData['quantity_limit'] : 10;
        $collection->setPageSize($_pSize);
        $collection->setCurPage(1);
        return $collection;
    }

    public function getProductsCollection() {
        if(is_null($this->_collection) && !is_null($this->getAFPBlock()->getAutomationType())) {
            switch($this->getAFPBlock()->getAutomationType()) {
                case AW_Featured_Model_Source_Automation::NONE:
                    $this->_collection = $this->_getCollectionForIds();
                    break;
                case AW_Featured_Model_Source_Automation::RANDOM:
                    $this->_collection = $this->_getRandomProductsCollection();
                    break;
                case AW_Featured_Model_Source_Automation::TOPSELLERS:
                    $this->_collection = $this->_getTopSellersCollection();
                    break;
                case AW_Featured_Model_Source_Automation::TOPRATED:
                    $this->_collection = $this->_getTopRatedCollection();
                    break;
                case AW_Featured_Model_Source_Automation::MOSTREVIEWED:
                    $this->_collection = $this->_getMostReviewedCollection();
                    break;
                case AW_Featured_Model_Source_Automation::RECENTLYADDED:
                    $this->_collection = $this->_getRecentlyAddedCollection();
                    break;
                case AW_Featured_Model_Source_Automation::CURRENTCATEGORY:
                    $this->_collection = $this->_getCurrentCategoryCollection();
                    break;
                default:
                    $this->_collection = $this->_prepareCollection(Mage::getModel('awfeatured/product_collection'));
                    $this->setIsEmpty(true);
                    break;
            }
            $this->_collection->addMinimalPrice();
            $this->_collection->addAttributeToSelect(array('name', 'short_description', 'small_image', 'thumbnail', 'image'));
        }
        return $this->_collection;
    }

    public function getAFPBlock() {
        return $this->_awafpblock;
    }

    public function getAFPBlockAutomationData($key = null) {
        $_value = null;
        if($this->getAFPBlock()) {
            $_automationData = $this->getAFPBlock()->getAutomationData();
            $_value = is_null($key) ? $_automationData : (isset($_automationData[$key]) ? $_automationData[$key] : null);
        }
        return $_value;
    }

    public function getAFPBlockTypeData($key = null) {
        $_value = null;
        if($this->getAFPBlock()) {
            $_typeData = $this->getAFPBlock()->getTypeData();
            $_value = is_null($key) ? $_typeData : (isset($_typeData[$key]) ? $_typeData[$key] : null);
        }
        return $_value;
    }

    public function canDisplay() {
        if($this->getAFPBlock()) {
            if(array_intersect($this->getAFPBlock()->getStore(), array(0, Mage::app()->getStore()->getId())) == array()) return false;
            if(!$this->getAFPBlock()->getIsActive()) return false;
            return true;
        }
        return false;
    }

    public function getUniqueBlockId() {
        if(is_null($this->_uniqueBlockId))
                $this->_uniqueBlockId = uniqid('awafpblock');
        return $this->_uniqueBlockId;
    }

    public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false) {
        if(Mage::helper('awfeatured')->checkVersion('1.4.1.1'))
                return parent::stripTags($data, $allowableTags, $allowHtmlEntities);
        else
            return $this->htmlEscape($data);
    }
}
