<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Menu_Model_System_Config_Top_Placement
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('ammenu')->__('Before Categories')),
            array('value' => 1, 'label' => Mage::helper('ammenu')->__('After Categories')),
            array('value' => 2, 'label' => Mage::helper('ammenu')->__('Instead of the Categories')),
            array('value' => 3, 'label' => Mage::helper('ammenu')->__('Do not show')),
        );
    }

}
