<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('whlly_stores')}
    ADD COLUMN `address1` VARCHAR(255) NOT NULL AFTER `store_name`,
	ADD COLUMN `address2` VARCHAR(255) NOT NULL AFTER `address1`,
	ADD COLUMN `telephone` VARCHAR(255) NOT NULL AFTER `address2`,
	ADD COLUMN `email` VARCHAR(255) NOT NULL AFTER `telephone`
");

$installer->endSetup();