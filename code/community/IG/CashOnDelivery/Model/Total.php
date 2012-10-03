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

class IG_CashOnDelivery_Model_Total extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
	protected function getSession()
	{
		if (Mage::getDesign()->getArea() == 'adminhtml')
			return Mage::getSingleton('adminhtml/session_quote');

		return Mage::getSingleton('checkout/session');
	}

	protected function getQuote()
	{
		return $this->getSession()->getQuote();
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$model = Mage::getModel('ig_cashondelivery/cashondelivery');
		$amount = $address->getShippingAmount();

		if (
			(($amount != 0) || $address->getShippingDescription()) &&
			($this->getQuote()->getPayment()->getMethod() == $model->getCode()) &&
			($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
		) {
			$fee		= $model->getExtraFee();
			$fee_excl	= Mage::helper('tax')->getShippingPrice($fee, false);

			$address->setShippingAmount($address->getShippingAmount()+$fee_excl);
			$address->setBaseShippingAmount($address->getBaseShippingAmount()+$fee_excl);

			$address->setShippingDescription($address->getShippingDescription().' + '.$model->getTitle());
		}

		return $this;
	}
}
