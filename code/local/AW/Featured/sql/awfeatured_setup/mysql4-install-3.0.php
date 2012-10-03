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

$installer = $this;
$installer->startSetup();

try {
    $installer->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('awfeatured/blocks')}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `block_name` TINYTEXT NOT NULL ,
            `block_id` TINYTEXT NOT NULL ,
            `store` TINYTEXT NOT NULL ,
            `type` TINYTEXT NOT NULL ,
            `type_data` TEXT NOT NULL ,
            `autoposition` INT NOT NULL ,
            `automation_type` INT NOT NULL ,
            `automation_data` TEXT NOT NULL ,
            `is_active` TINYINT( 1 ) NOT NULL DEFAULT '1'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'All AFP blocks are stored in this table';
    ");
} catch (Exception $ex) {
    Mage::logException($ex);
}

$installer->endSetup();
