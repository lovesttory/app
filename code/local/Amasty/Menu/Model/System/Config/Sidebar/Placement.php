<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Menu_Model_System_Config_Sidebar_Placement
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('ammenu')->__('Do not show')),
            array('value' => 'menu_left', 'label' => Mage::helper('ammenu')->__('Left sidebar')),
            array('value' => 'menu_right', 'label' => Mage::helper('ammenu')->__('Right sidebar'))            
        );
    }

}
