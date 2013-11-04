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

class Aitoc_Aitpermissions_Model_Rewrite_AdminSystemStore extends Mage_Adminhtml_Model_System_Store
{
    public function __construct()
    {
        parent::__construct();

        if (Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled())
        {
            $this->setIsAdminScopeAllowed(false);
        }
    }
    
    protected function _loadWebsiteCollection()
    {
        $this->_websiteCollection = Mage::app()->getWebsites();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_websiteCollection as $id => $website)
            {
                if (!in_array($id, $role->getAllowedWebsiteIds()))
                {
                    unset($this->_websiteCollection[$id]);
                }
            }
        }
        
        return $this;
    }
    
    protected function _loadStoreCollection()
    {
        $this->_storeCollection = Mage::app()->getStores();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_storeCollection as $id => $store)
            {
                if (!in_array($id, $role->getAllowedStoreviewIds()))
                {
                    unset($this->_storeCollection[$id]);
                }
            }
        }

        return $this;
    }
}