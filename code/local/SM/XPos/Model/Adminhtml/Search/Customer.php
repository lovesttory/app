<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Customer Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SM_XPos_Model_Adminhtml_Search_Customer extends Varien_Object {

    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Customer
     */
    public function load() {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect(array('firstname', 'lastname', 'telephone', 'email'))
                ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
                ->addAttributeToFilter(array(
                    array('attribute' => 'firstname', 'like' => $this->getQuery() . '%'),
                    array('attribute' => 'lastname', 'like' => $this->getQuery() . '%'),
                    array('attribute' => 'telephone', 'like' => $this->getQuery() . '%'),
                    array('attribute' => 'email', 'like' => '%' . $this->getQuery() . '%'),
                ))
                ->setPage($this->getStart(), $this->getLimit())
                ->load();

        foreach ($collection->getItems() as $customer) {
            $arr[] = array(
                'id' => $customer->getId(),
                'type' => Mage::helper('adminhtml')->__('Customer'),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'description' => $customer->getCompany(),
                'telephone' => $customer->getTelephone(),
            );
        }

        $this->setResults($arr);

        return $this;
    }

    public function loadAll($limit, $page) {
        $arr = array();


        $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect(array('firstname', 'lastname', 'telephone', 'email'))
                ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
                ->setCurPage($page)
                ->setPageSize($limit)
                ->load();

        foreach ($collection->getItems() as $customer) {
            $arr[] = array(
                'id' => $customer->getId(),
                'type' => Mage::helper('adminhtml')->__('Customer'),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'description' => $customer->getCompany(),
                'telephone' => $customer->getTelephone(),
            );
        }

        $this->setResults($arr);

        return $this;
    }

}
