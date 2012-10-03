<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Menu_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('ammenu')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('ammenu')->__('Disabled')
        );
    }
}