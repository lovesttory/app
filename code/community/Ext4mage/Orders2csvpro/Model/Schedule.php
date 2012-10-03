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
class Ext4mage_Orders2csvpro_Model_Schedule extends Mage_Core_Model_Abstract
{

	protected $_column = array();
	protected $_columnInstance;

	public function _construct()
	{
		parent::_construct();
		$this->_init('orders2csvpro/schedule');
		
	}
	
	/**
	* Retrieve active schedules
	*
	* @return collection
	*/
	public function getActiveSchedules()
	{
		$collection = $this->getCollection()
			->addFieldToFilter('is_active', '1')
			->setOrder('title', 'DESC');
		
		return $collection;
	}
}