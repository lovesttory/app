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
class Ext4mage_Orders2csvpro_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getItemByProductId($itemId,$itemCollection)
	{
		foreach ($itemCollection as $item) {
			if ($item->getProductId()==$itemId) {
				return $item;
			}
		}
		return false;
	}
	
	public function getCurrencyKeys() {
	  $valueOptions = array(
		'order_data_discount_amount',
		'order_data_discount_canceled',
		'order_data_discount_invoiced',
		'order_data_discount_refunded',
		'order_data_grand_total',
		'order_data_shipping_amount',
		'order_data_shipping_canceled',
		'order_data_shipping_invoiced',
		'order_data_shipping_refunded',
		'order_data_shipping_tax_amount',
		'order_data_shipping_tax_refunded',
		'order_data_subtotal',
		'order_data_subtotal_canceled',
		'order_data_subtotal_invoiced',
		'order_data_subtotal_refunded',
		'order_data_tax_amount',
		'order_data_tax_canceled',
		'order_data_tax_invoiced',
		'order_data_tax_refunded',
		'order_data_total_canceled',
		'order_data_total_invoiced',
		'order_data_total_offline_refunded',
		'order_data_total_online_refunded',
		'order_data_total_paid',
		'order_data_total_refunded',
		'order_data_shipping_discount_amount',
		'order_data_subtotal_incl_tax',
		'order_data_total_due',
		'order_data_shipping_incl_tax',
		'item_data_price',
		'item_data_original_price',
		'item_data_tax_amount',
		'item_data_tax_invoiced',
		'item_data_discount_amount',
		'item_data_discount_invoiced',
		'item_data_amount_refunded',
		'item_data_row_total',
		'item_data_row_invoiced',
		'item_data_tax_before_discount',
		'item_data_price_incl_tax',
	    'item_data_row_total_incl_tax'	   
	  );
	  return $valueOptions;
	}
}