<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Featured
 * @version    3.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

class AW_Featured_Model_Source_Status extends AW_Featured_Model_Source_Abstract {
    const ENABLED = 1;
    const DISABLED = 0;

    const ENABLED_LABEL = 'Enabled';
    const DISABLED_LABEL = 'Disabled';

    public function toOptionArray() {
        return array(
            array('value' => self::ENABLED, 'label' => Mage::helper('awfeatured')->__(self::ENABLED_LABEL)),
            array('value' => self::DISABLED, 'label' => Mage::helper('awfeatured')->__(self::DISABLED_LABEL))
        );
    }
}
