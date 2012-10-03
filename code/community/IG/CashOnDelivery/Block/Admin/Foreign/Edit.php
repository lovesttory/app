<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@idealiagroup.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category   IG
 * @package    IG_CashOnDelivery
 * @copyright  Copyright (c) 2010-2011 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Riccardo Tempesta <tempesta@idealiagroup.com>
*/
 
class IG_CashOnDelivery_Block_Admin_Foreign_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId	= 'id';
		$this->_blockGroup	= 'ig_cashondelivery';
		$this->_controller	= 'admin_foreign';
		$this->_updateButton('save', 'label', Mage::helper('ig_cashondelivery')->__('Save Rule'));
		$this->_updateButton('delete', 'label', Mage::helper('ig_cashondelivery')->__('Delete Rule'));
	}
	
	public function getHeaderText()
	{
		if( Mage::registry('ig_cashondelivery_data') && Mage::registry('ig_cashondelivery_data')->getId() ) {
			return Mage::helper('ig_cashondelivery')->__("Edit Rule");
		} else {
			return Mage::helper('ig_cashondelivery')->__('Add Rule');

		}
	}
}