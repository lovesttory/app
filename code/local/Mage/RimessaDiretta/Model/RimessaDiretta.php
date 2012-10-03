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
 * @package    Mage_RimessaDiretta
 * @copyright  Copyright (c) 2012 TeleType - Oto Tortorella
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_RimessaDiretta_Model_RimessaDiretta extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'rimessadiretta';

    protected $_formBlockType = 'rimessaDiretta/form';
    protected $_infoBlockType = 'rimessaDiretta/info';

    public function getAccountHolder()
    {
        return $this->getConfigData('accountholder');
    }

    public function getAccountNumber()
    {
        return $this->getConfigData('accountnumber');
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
