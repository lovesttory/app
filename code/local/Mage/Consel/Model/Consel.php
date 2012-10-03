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
 * @package    Mage_Consel
 * @copyright  Copyright (c) 2009 TeleType - Oto tortorella
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Consel_Model_Consel extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'consel';

    protected $_formBlockType = 'consel/form';
    protected $_infoBlockType = 'consel/info';
	/*
    public function getConvenzName()
    {
        return $this->getConfigData('conselconvenzname');
    }

    public function getConvenzNumber()
    {
        return $this->getConfigData('conselconvenznumber');
    }
	*/
    public function getPayWithinXDays()
    {
        return intval($this->getConfigData('paywithinxdays'));
    }

    public function getCustomText()
    {
        return nl2br($this->getConfigData('customtext'));
    }

}
