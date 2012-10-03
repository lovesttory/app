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
class Ext4mage_Orders2csvpro_Block_Adminhtml_Schedule_Edit_Tab_Generel extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('generel_fieldset', array('legend'=>Mage::helper('orders2csvpro')->__('Element information')));
		 
		$fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('orders2csvpro')->__('Title (email subject)'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		));
		
		$fieldset->addField('is_active', 'select', array(
          'label'     => Mage::helper('orders2csvpro')->__('Is active'),
          'name'      => 'is_active',
          'required'  => true,
          'values'    => array(
				array(
                  'value'     => 1,
                  'label'     => Mage::helper('orders2csvpro')->__('Enabled'),
				),
				array(
                  'value'     => 2,
                  'label'     => Mage::helper('orders2csvpro')->__('Disabled'),
				),
			),
		));

		$fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('orders2csvpro')->__('Receiver email-address'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
		));
		
		$fieldset->addField('file_id', 'select', array(
          'label'     => Mage::helper('orders2csvpro')->__('File structure to use'),
          'name'      => 'file_id',
          'required'  => true,
          'values'    => Mage::getSingleton('orders2csvpro/file')->getFilesForForm()
		));
		
		$orderConfig = Mage::getModel('sales/order_config');
		$statusValues[] = array(
            'label' => Mage::helper('orders2csvpro')->__('All statuses'),
            'value' => "all"
		);
		foreach ($orderConfig->getStatuses() as $code => $label) {
			$statusValues[] = array(
	            'label' => $label,
	            'value' => $code
			);
		}
		
		$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('orders2csvpro')->__('Order status to include'),
          'name'      => 'status',
          'required'  => true,
          'values'    => $statusValues
		));
		
		$fieldset->addField('periode', 'select', array(
		          'label'     => Mage::helper('orders2csvpro')->__('Run this'),
		          'name'      => 'periode',
		          'required'  => true,
		          'values'    => array(
					array(
		                  'value'     => 1,
		                  'label'     => Mage::helper('orders2csvpro')->__('Hourly at 05 minuts'),
					),
					array(
		                  'value'     => 2,
		                  'label'     => Mage::helper('orders2csvpro')->__('Daily at 00:05'),
					),
					array(
		                  'value'     => 3,
		                  'label'     => Mage::helper('orders2csvpro')->__('Weekly - Sunday morning at 00:05'),
					),
					array(
		                  'value'     => 4,
		                  'label'     => Mage::helper('orders2csvpro')->__('Monthly - on the 1. day at 00:05'),
					),
					array(
		                  'value'     => 5,
		                  'label'     => Mage::helper('orders2csvpro')->__('Quarterly - every 3 months'),
					),
					array(
		                  'value'     => 6,
		                  'label'     => Mage::helper('orders2csvpro')->__('Semi-annually - twice a year'),
					),
					array(
		                  'value'     => 7,
		                  'label'     => Mage::helper('orders2csvpro')->__('Annually - on the 01/01'),
					),
				  ),
		));
		
		$fieldset->addField('show_header', 'select', array(
          'label'     => Mage::helper('orders2csvpro')->__('Include header fields'),
          'name'      => 'show_header',
          'required'  => true,
          'values'    => array(
				array(
                  'value'     => 1,
                  'label'     => Mage::helper('orders2csvpro')->__('Yes'),
				),
				array(
                  'value'     => 2,
                  'label'     => Mage::helper('orders2csvpro')->__('No'),
				),
			),
		));
		

		$fieldset->addField('attached', 'select', array(
		          'label'     => Mage::helper('orders2csvpro')->__('Attache output as file'),
		          'name'      => 'attached',
		          'required'  => true,
		          'values'    => array(
					array(
		                  'value'     => 1,
		                  'label'     => Mage::helper('orders2csvpro')->__('Yes - attache as CSV file'),
					),
					array(
		                  'value'     => 2,
		                  'label'     => Mage::helper('orders2csvpro')->__('No - put output into email'),
					),
			),
		));
		
		
		$fieldset->addField('saveas', 'hidden', array(
	        'name'      => 'saveas',
	        'value'     => '0',
		));

		if ( Mage::getSingleton('adminhtml/session')->getOrders2csvproData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getOrders2csvproData());
			Mage::getSingleton('adminhtml/session')->setScheduleData(null);
		} elseif ( Mage::registry('orders2csvpro_data') ) {
			$form->setValues(Mage::registry('orders2csvpro_data')->getData());
		}
		return parent::_prepareForm();
	}
}