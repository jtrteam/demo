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

class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderCreateStoreSelect
    extends Mage_Adminhtml_Block_Sales_Order_Create_Store_Select
{
    public function getStoreCollection($group)
    {
        $stores = parent::getStoreCollection($group);

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
        	$stores->addIdFilter($role->getAllowedStoreviewIds());
        }

        return $stores;
    }
}