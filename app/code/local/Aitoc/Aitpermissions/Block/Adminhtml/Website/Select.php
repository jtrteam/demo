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
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/
class Aitoc_Aitpermissions_Block_Adminhtml_Website_Select extends Mage_Core_Block_Template
{
    protected $_websiteIds = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/website_select.phtml');
    }
    
    public function getWebsites()
    {
        $websites = Mage::app()->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) 
        {
            foreach ($websites as $websiteId => $website) 
            {
                if (!in_array($websiteId, $websiteIds)) 
                {
                    unset($websites[$websiteId]);
                }
            }
        }
        return $websites;
    }
    
    public function setCurrentWebsiteIds($websiteIds)
    {
        $this->_websiteIds = $websiteIds;
        return $this;
    }
    
    public function getCurrentWebsiteIds()
    {
        return $this->_websiteIds;
    }
}