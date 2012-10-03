<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Mage_BankPrePayment
 * @copyright  Copyright (c) 2008 TeleType - Oto Tortorella
 * @copyright  Created from Mage_BankPayment
 * @copyright  Copyright (c) 2008 Andrej Sinicyn
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_BankPrePayment_Model_BankPrePayment extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'bankprepayment';

    protected $_formBlockType = 'bankPrePayment/form';
    protected $_infoBlockType = 'bankPrePayment/info';

    public function getAccountHolder()
    {
        return $this->getConfigData('bankaccountholder');
    }

    public function getAccountNumber()
    {
        return $this->getConfigData('bankaccountnumber');
    }

    public function getSortCode()
    {
        return $this->getConfigData('sortcode');
    }

    public function getBankName()
    {
        return $this->getConfigData('bankname');
    }

    public function getIBAN()
    {
        return $this->getConfigData('bankiban');
    }

    public function getBIC()
    {
        return $this->getConfigData('bankbic');
    }

    public function getPayWithinXDays()
    {
        return intval($this->getConfigData('paywithinxdays'));
    }

    public function getCustomText()
    {
        return nl2br($this->getConfigData('customtext'));
    }

}
