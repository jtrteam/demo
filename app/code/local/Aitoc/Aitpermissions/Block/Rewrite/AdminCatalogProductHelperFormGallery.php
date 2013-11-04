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

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductHelperFormGallery
    extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery
{
	public function getElementHtml()
	{
		$html = parent::getElementHtml();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
		{
            $html = preg_replace(
                '@cell-remove a-center last"><input([ ]+)type="checkbox"@',
                'cell-remove a-center last"><input disabled="disabled" type="checkbox"',
                $html
            );
		}

        return $html;
	}
}