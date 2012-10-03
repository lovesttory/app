<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
 */
if (version_compare(Mage::getVersion(), '1.6') < 0) {
	class Amasty_Menu_Model_Generic_Menu_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
	{
		protected function _construct()
		{
			
		}
	}
} else {
	class Amasty_Menu_Model_Generic_Menu_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
	{
		protected function _construct()
		{
	   	
		}
	}
}
