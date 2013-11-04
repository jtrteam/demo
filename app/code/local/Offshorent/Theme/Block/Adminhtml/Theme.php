<?php
class Offshorent_Theme_Block_Adminhtml_Theme extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_theme';
    $this->_blockGroup = 'theme';
    $this->_headerText = Mage::helper('theme')->__('Change Your Store Theme');
    parent::__construct();
  }
}