<?php
/**
* Ext4mage Orders2csvpro Module
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
*
* @category   Ext4mage
* @package    Ext4mage_Orders2csvpro
* @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @author     Henrik Kier <info@ext4mage.com>
* */
class Ext4mage_Orders2csvpro_Model_Mysql4_Runs extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the orders2csvpro_id refers to the key field in your database table.
		$this->_init('orders2csvpro/runs', 'runs_id');
		
	}

	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$object->setRunTime(Mage::getSingleton('core/date')->gmtDate());
		return $this;
	}
	
	// Get all orders that has not been in a run and that has the right statuses
	// return array of order objects
	public function getAllOrdersNotRun($schedule){
		$select = $this->_getReadAdapter()->select();
		
		$select->from(array('o' => $this->getTable('sales/order')), array('o.entity_id'))
			->joinLeft(array('runs' => $this->getMainTable()), 'runs.order_id = o.entity_id AND runs.schedule_id = '.$schedule->getId(), array())
			->where('runs.order_id IS NULL');
		if(strcmp($schedule->getStatus(),'All') != 0){
			$select->where('o.status = ?', $schedule->getStatus());
		}

		return $this->_getReadAdapter()->fetchAll($select);
	}
}