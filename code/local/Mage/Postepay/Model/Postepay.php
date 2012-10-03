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
 * @package    Mage_Postepay
 * @copyright  Copyright (c) 2009 TeleType - Oto tortorella
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Postepay_Model_Postepay extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'postepay';

    protected $_formBlockType = 'postepay/form';
    protected $_infoBlockType = 'postepay/info';

    public function getCardHolder()
    {
        return $this->getConfigData('postepayholder');
    }

    public function getCardNumber()
    {
        return $this->getConfigData('postepaynumber');
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
