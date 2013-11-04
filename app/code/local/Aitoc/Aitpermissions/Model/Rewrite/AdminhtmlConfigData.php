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

class Aitoc_Aitpermissions_Model_Rewrite_AdminhtmlConfigData extends Mage_Adminhtml_Model_Config_Data
{

    public function load()
    {
        if ($this->getSection() != Mage::app()->getRequest()->getParam('section')) {
            $this->setSection(Mage::app()->getRequest()->getParam('section'));
            $this->_configData = null;
        }
        return parent::load();
    }
}