<?php

class Offshorent_Theme_Block_Adminhtml_Theme_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'theme';
        $this->_controller = 'adminhtml_theme';
        
        $this->_updateButton('save', 'label', Mage::helper('theme')->__('Save Theme'));
    }

   public function getHeaderText()
    {
                 return Mage::helper('theme')->__('Change Your Store Theme');
    }
    
}