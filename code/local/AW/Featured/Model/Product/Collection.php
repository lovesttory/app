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

class AW_Featured_Model_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection {
    private $_awafpOveralRatingJoined = false;
    private $_awafpReviewsCountJoined = false;
    private $_awafpOrderedQtyJoined = false;

    public function addUrlRewrites() {
        $this->getSelect()->joinLeft(array('urwr' => $this->getTable('core/url_rewrite')),
            '(urwr.product_id=e.entity_id) AND (urwr.store_id='.$this->getStoreId().')',
            array('request_path'));
        return $this;
    }

    public function addOrderedQty($showOnlyOrdered = true, $hasPid = false) {
        if(Mage::helper('awfeatured')->checkVersion('1.4')) {
            $this->getSelect()->joinLeft(array('order_items' => $this->getTable('sales/order_item')),
                '(order_items.product_id = e.entity_id) AND (order_items.store_id='.$this->getStoreId().')',
                array('qty_ordered' => 'SUM(order_items.qty_ordered)')
            );
        } else {
            //1.3.x support
            $this->getSelect()->joinLeft(array('order_items' => $this->getTable('sales/order_item')),
                '(order_items.product_id = e.entity_id)',
                array('qty_ordered' => 'SUM(order_items.qty_ordered)')
            );
            $this->getSelect()->joinLeft(array('order' => $this->getTable('sales/order')),
                '(order_items.order_id=order.entity_id) AND (order.store_id='.$this->getStoreId().')',
                array('order_store_id' => 'order.store_id')
            );
            $this->getSelect()->where('order.store_id = ?', $this->getStoreId());
        }
        $this->getSelect()->where('order_items.parent_item_id IS'.($hasPid ? ' NOT ' : ' ').'NULL');
        $this->_awafpOrderedQtyJoined = true;
        if($showOnlyOrdered)
            $this->getSelect()->where('qty_ordered>0');
        return $this;
    }

    public function sortByOrderedQty($desc = true) {
        if($this->_awafpOrderedQtyJoined)
            $this->getSelect()->order('qty_ordered '.($desc ? 'desc' : 'asc'));
        return $this;
    }

    /**
     * Selecting products from multiple categories
     * @param string $categories categories list separated by commas
     * @return AW_Featured_Model_Product_Collection
     */
    public function addCategoriesFilter($categories, $includeSubCategories = false) {
        if(is_array($categories))
            $categories = @implode(',', $categories);
        $alias = 'cat_index';
        $categoryCondition = $this->getConnection()->quoteInto(
            $alias.'.product_id=e.entity_id'.($includeSubCategories ? '' : ' AND '.$alias.'.is_parent=1').' AND '.$alias.'.store_id=? AND ',
            $this->getStoreId());
        $categoryCondition.= $alias.'.category_id IN ('.$categories.')';
        $this->getSelect()->joinInner(
            array($alias => $this->getTable('catalog/category_product_index')),
                $categoryCondition,
                array('position'=>'position')
        );
        $this->_categoryIndexJoined = true;
        $this->_joinFields['position'] = array('table'=>$alias, 'field'=>'position' );
        return $this;
    }

    public function joinOveralRating($storeId = null) {
        if(is_null($storeId)) $storeId = Mage::app()->getStore()->getId();
        $this->joinField('rating_summary', $this->getTable('review/review_aggregate'), 'rating_summary', 'entity_pk_value=entity_id', array('store_id' => $storeId), 'left');
        $this->_awafpOveralRatingJoined = true;
        return $this;
    }

    public function sortByRating($desc = true) {
        if($this->_awafpOveralRatingJoined)
            $this->addAttributeToSort('rating_summary', $desc ? 'desc' : 'asc');
        return $this;
    }

    public function joinReviewsCount($storeId = null) {
        if(is_null($storeId)) $storeId = Mage::app()->getStore()->getId();
        $this->joinField('reviews_count', $this->getTable('review/review_aggregate'), 'reviews_count', 'entity_pk_value=entity_id', array('store_id' => $storeId), 'left');
        $this->_awafpReviewsCountJoined = true;
        return $this;
    }

    public function sortByReviewsCount($desc = true) {
        if($this->_awafpReviewsCountJoined)
            $this->addAttributeToSort('reviews_count', $desc ? 'desc' : 'asc');
        return $this;
    }

    /**
     * Covers bug in Magento function
     * @return Varien_Db_Select
     */
    public function getSelectCountSql() {
        $catalogProductFlatHelper = Mage::helper('catalog/product_flat');
        if($catalogProductFlatHelper && $catalogProductFlatHelper->isEnabled())
            return parent::getSelectCountSql();
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }
}
