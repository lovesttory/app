<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_ListingsTemplatesCalculatedShipping extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('M2ePro/ListingsTemplatesCalculatedShipping', 'listing_template_id');
        $this->_isPkAutoIncrement = false;
    }
}