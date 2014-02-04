<?php

class Whlly_Stores_Block_Adminhtml_Stores_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('stores_form', array('legend'=>Mage::helper('stores')->__('Store information')));
     
      $fieldset->addField('store_name', 'text', array(
          'label'     => Mage::helper('stores')->__('Store Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'store_name',
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'  => 'content',
          'label' => Mage::helper('stores')->__('Content'),
		  'title' => Mage::helper('stores')->__('Content'),
		  'style' => 'width:600px; height:300px;',
		  'config' => Mage::getSingleton('stores/wysiwyg_config')->getConfig(),
		  'wysiwyg' => true,
		  'required' => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getStoresData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getStoresData());
          Mage::getSingleton('adminhtml/session')->setStoresData(null);
      } elseif ( Mage::registry('stores_data') ) {
          $form->setValues(Mage::registry('stores_data')->getData());
      }
      return parent::_prepareForm();
  }
}