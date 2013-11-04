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

$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->updateAttribute('catalog_product', 'created_by', 'is_visible', '0'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'source_model', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_label', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_input', ''); 

$installer->endSetup();