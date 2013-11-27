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
          'name'      => 'facebook',
        ));
		$fieldset->addField('twitter', 'text', array(
          'label'     => Mage::helper('customer')->__('Twitter'),
          'name'      => 'twitter',
        ));
		$fieldset->addField('pinterest', 'text', array(
          'label'     => Mage::helper('customer')->__('Pinterest'),
          'name'      => 'pinterest',
        ));
		$fieldset->addField('instagram', 'text', array(
          'label'     => Mage::helper('customer')->__('Instagram'),
          'name'      => 'instagram',
        ));
        $google = $fieldset->addField('googleplus', 'text', array(
          'label'     => Mage::helper('customer')->__('Google +'),
          'name'      => 'googleplus',
        ));
		
		$customer = Mage::registry('current_customer');
        $form->setValues($customer->getData());
        $this->setForm($form);
        return $this;
    }

   
}
