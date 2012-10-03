<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Menu_Model_Resource_Menu extends Amasty_Menu_Model_Generic_Menu
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store  = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('ammenu/menu', 'menu_id');        
    }
    
 	/**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Amasty_Menu_Model_Resource_Menu
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }

        return parent::_afterLoad($object);
    }

    /**
     * Process page data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Amasty_Menu_Model_Resource_Menu
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'menu_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('ammenu/menu_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Amasty_Menu_Model_Menu_Resource
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        unset($object->stores);
        return parent::_beforeSave($object);
    }

    /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Amasty_Menu_Model_Resource_Menu
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('ammenu/menu_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'menu_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'menu_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }


    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($menuId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('ammenu/menu_store'), 'store_id')
            ->where('menu_id = ?',(int)$menuId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Amasty_Menu_Model_Resource_Menu
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }
}
