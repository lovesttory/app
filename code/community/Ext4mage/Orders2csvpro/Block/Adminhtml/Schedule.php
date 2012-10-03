<?php
/**
* Ext4mage Orders2csvpro Module
*
* NOTICE OF LICENSE
*
* This source schedule is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the schedule LICENSE.txt.
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
class Ext4mage_Orders2csvpro_Block_Adminhtml_Schedule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_schedule';
		$this->_blockGroup = 'orders2csvpro';
		$this->_headerText = Mage::helper('orders2csvpro')->__('Orders2CSV PRO Schedule');
		$this->_addButtonLabel = Mage::helper('orders2csvpro')->__('Create new schedule');
		parent::__construct();
	}
}