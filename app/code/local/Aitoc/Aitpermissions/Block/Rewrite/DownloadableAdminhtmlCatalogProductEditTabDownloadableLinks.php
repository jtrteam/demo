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
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_DownloadableAdminhtmlCatalogProductEditTabDownloadableLinks
    extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
{
    public function getPurchasedSeparatelySelect()
    {        
        $html = parent::getPurchasedSeparatelySelect();

        $role = Mage::getSingleton('aitpermissions/role');

        if (!Mage::app()->isSingleStoreMode() && 
            $role->isPermissionsEnabled() &&
            !$role->canEditGlobalAttributes())
        {
            $html = str_replace('<select', '<select disabled="disabled"', $html);         
        }

        return $html;
    }
}