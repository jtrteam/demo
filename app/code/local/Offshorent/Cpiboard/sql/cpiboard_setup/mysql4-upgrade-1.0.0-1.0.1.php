<?php
/**
* @copyright Offshorent.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('offshorent_cpi_definition')}` ADD `store_id` int(11) unsigned NOT NULL AFTER `cpi_id` 
");
$this->endSetup();