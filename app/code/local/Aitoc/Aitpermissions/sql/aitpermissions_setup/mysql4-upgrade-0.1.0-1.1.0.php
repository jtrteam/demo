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
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD `website_id` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `role_id` ;

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD INDEX ( `website_id` ) ;

");

$installer->endSetup();