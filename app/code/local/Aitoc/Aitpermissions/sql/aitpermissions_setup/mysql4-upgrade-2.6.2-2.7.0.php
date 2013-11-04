<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.7.5
 * @license:     Y6BG0eVRwBRlH3J38c7aWdqCvw6zDo9AlBIeUllr4w
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'created_by', 'source_model', 'Aitoc_Aitpermissions_Model_Source_Admins'); 


$installer->run($sql = "
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoc_aitpermissions_approvedcategories')} (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `category_id` smallint(5) unsigned NOT NULL,
  `is_approved` smallint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
    
");

$installer->endSetup();