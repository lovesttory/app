<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('ammenu/menu')}`;
CREATE TABLE  `{$this->getTable('ammenu/menu')}` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cms_page_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `modified` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `block_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('ammenu/menu')}` VALUES (1, 0, 'Root page', NULL, 1, 0, 0, 0, 0);

DROP TABLE IF EXISTS `{$this->getTable('ammenu/menu_store')}`;
CREATE TABLE  `{$this->getTable('ammenu/menu_store')}` (
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`menu_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('ammenu/menu_store')}` select m.menu_id, 0 from `{$this->getTable('ammenu/menu')}` as m;

    ");

$installer->endSetup(); 