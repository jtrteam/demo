<?php

class Offshorent_Cpiboard_Block_Adminhtml_Cpiboard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'cpiboard';
        $this->_controller = 'adminhtml_cpiboard';
        
        $this->_updateButton('save', 'label', Mage::helper('cpiboard')->__('Save Definition'));
		$this->_updateButton('delete', 'label', Mage::helper('cpiboard')->__('Delete Definition'));
		
		/*--- Admin user check-------------*/	
		$roleId = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
		$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
		if ($roleName != 'Administrators') {
			$this->_removeButton('delete');
		}
		/*--- Admin user check-------------*/
       
    }

    public function getHeaderText()
    {
        if( Mage::registry('cpiboard_data') && Mage::registry('cpiboard_data')->getId() ) {
            return Mage::helper('cpiboard')->__("Edit Definition '%s'", $this->htmlEscape(Mage::registry('cpiboard_data')->getTitle()));
        } else {
            return Mage::helper('cpiboard')->__('Add Definition');
        }
    }
}