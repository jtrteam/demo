<?php
class Offshorent_Cpiboard_Block_Adminhtml_Cpiboard extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cpiboard';
    $this->_blockGroup = 'cpiboard';
    $this->_headerText = Mage::helper('cpiboard')->__('CPI Definition');
	$this->_addButtonLabel = Mage::helper('cpiboard')->__('Add Definition');
    parent::__construct();
	
	$this->setTemplate('cpiboard/cpiboard.phtml');
	
	/*--- Admin user check-------------*/
	
	$roleId = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
	$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
	if ($roleName != 'Administrators') {
		$this->_removeButton('add');
	}
		
	/*--- Admin user check-------------*/
	
  }
}