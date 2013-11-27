<?php

class Offshorent_Socialaccounts_Block_Adminhtml_Socialaccounts_Edit_Tab_Social extends Mage_Adminhtml_Block_Widget_Form
{
     
    public function initForm()
    {
         $form = new Varien_Data_Form();
         $this->setForm($form);
		 
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('customer')->__('Social Account Edits')
        ));
        $fieldset->addField('facebook', 'text', array(
          'label'     => Mage::helper('customer')->__('Facebook'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'facebook',
        ));
		$fieldset->addField('twitter', 'text', array(
          'label'     => Mage::helper('customer')->__('Twitter'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'twitter',
        ));
		$fieldset->addField('pinterest', 'text', array(
          'label'     => Mage::helper('customer')->__('Pinterest'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'pinterest',
        ));
		$fieldset->addField('instagram', 'text', array(
          'label'     => Mage::helper('customer')->__('Instagram'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'instagram',
        ));
        $google = $fieldset->addField('googleplus', 'text', array(
          'label'     => Mage::helper('customer')->__('Google +'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'googleplus',
        ));
		
		$customer = Mage::registry('current_customer');
        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }

   
}
