<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('whlly_stores')};
CREATE TABLE {$this->getTable('whlly_stores')} (
  `stores_id` int(11) unsigned NOT NULL auto_increment,
  `store_name` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  PRIMARY KEY (`stores_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 