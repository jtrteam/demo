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

class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererAttributeUrlkey
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey
{
    public function getElementHtml()
    {
        $html = parent::getElementHtml();
        $element = $this->getElement();
        
        if ($element && $element->getEntityAttribute() &&
            $element->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() && !$role->canEditGlobalAttributes())
            {
                $html = str_replace('type="text"', ' disabled="disabled" type="text"', $html);
            }
        }

        return $html;
    }
}