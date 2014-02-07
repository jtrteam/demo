<?php

class Whlly_Stores_Block_Adminhtml_Stores_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'stores';
        $this->_controller = 'adminhtml_stores';
        
        $this->_updateButton('save', 'label', Mage::helper('stores')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('stores')->__('Delete Item'));
		
		
    }
	
    public function getHeaderText()
    {
        if( Mage::registry('stores_data') && Mage::registry('stores_data')->getId() ) {
            return Mage::helper('stores')->__("Edit Store '%s'", $this->htmlEscape(Mage::registry('stores_data')->getStoreName()));
        } else {
            return Mage::helper('stores')->__('Add Store');
        }
    }
	
	protected function _prepareLayout()

    {
	   if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head')))
	   {
		$block->setCanLoadTinyMce(true);
	   }
         parent::_prepareLayout();
    }
}