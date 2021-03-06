<?php

class Offshorent_Theme_Block_Adminhtml_Theme_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save'),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
	  $fieldset = $form->addFieldset('theme_form', array('legend'=>Mage::helper('theme')->__('General information')));     
      if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => Mage::helper('core')->__('Your Store'),
                'title'    => Mage::helper('core')->__('Your Store'),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId(),
            ));
        }

        $fieldset->addField('design', 'select', array(
            'label'    => Mage::helper('core')->__('Store Design'),
            'title'    => Mage::helper('core')->__('Store Design'),
            'values'   => Mage::getSingleton('core/design_source_design')->getAllCustomOptions(),
            'name'     => 'design',
			'size'     => '5px',
            'required' => true,
			
        ));     
      
      return parent::_prepareForm();
  }
}