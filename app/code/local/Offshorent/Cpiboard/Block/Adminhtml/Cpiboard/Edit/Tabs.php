<?php

class Offshorent_Cpiboard_Block_Adminhtml_Cpiboard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('cpiboard_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cpiboard')->__('Definition Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cpiboard')->__('Definition Information'),
          'title'     => Mage::helper('cpiboard')->__('Definition Information'),
          'content'   => $this->getLayout()->createBlock('cpiboard/adminhtml_cpiboard_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}