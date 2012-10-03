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


class Mage_RimessaDiretta_Block_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rimessadiretta/form.phtml');
    }

}
