<?php
class Whlly_Stores_Block_Adminhtml_Stores extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_stores';
    $this->_blockGroup = 'stores';
    $this->_headerText = Mage::helper('stores')->__('Store Manager');
    $this->_addButtonLabel = Mage::helper('stores')->__('Add Store');
    parent::__construct();
  }
}