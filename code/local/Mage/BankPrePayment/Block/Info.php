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


class Mage_BankPrePayment_Block_Info extends Mage_Payment_Block_Info
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bankprepayment/info.phtml');
    }

}
