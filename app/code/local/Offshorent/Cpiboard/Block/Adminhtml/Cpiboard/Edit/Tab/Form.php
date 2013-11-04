<?php



class Offshorent_Cpiboard_Block_Adminhtml_Cpiboard_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form

{

  protected function _prepareForm() {

	  $titlecollection = Mage::getModel('cpiboard/cpi')->getCollection();

	  $titlecollection->load();

      $title = array();

	  foreach($titlecollection as $_cpi){

		$title[] = array(

                  'value'     => $_cpi->getId(),

                  'label'     => $_cpi->getTitle(),

              );

	  };	  

      $form = new Varien_Data_Form();

      $this->setForm($form);

      $fieldset = $form->addFieldset('cpiboard_form', array('legend'=>Mage::helper('cpiboard')->__('Definition Information')));
	  $fieldset->addField('cpi_id', 'select', array(

          'label'     => Mage::helper('cpiboard')->__('Title'),

          'name'      => 'cpi_id',

		  'values'    => $title,

      ));
	  
	 if (!Mage::app()->isSingleStoreMode()) {
		  $field = $fieldset->addField('store_id', 'select', array(
			  'label'    => Mage::helper('core')->__('Your Store'),
			  'title'    => Mage::helper('core')->__('Your Store'),
			  'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
			  'name'     => 'store[]',
			  'required' => true,
		  ));
		  $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		  $field->setRenderer($renderer);
	  } else {
		  $fieldset->addField('store_id', 'hidden', array(
			  'name'      => 'store[]',
			  'value'     => Mage::app()->getStore(true)->getId(),
		  ));
	  }

      $fieldset->addField('year', 'select', array(

          'label'     => Mage::helper('cpiboard')->__('Year'),

          'name'      => 'year',

          'values'    => array(

              array(

                  'value'     => 2013,

                  'label'     => 2013,

              ),

          ),

      ));
     

      for($i=1; $i<13;$i++){

		  $fieldset->addField(strtolower(date('M', mktime(0,0,0,$i))), 'text', array(

			  'label'     => Mage::helper('cpiboard')->__(date('M', mktime(0,0,0,$i))),

			  'required'  => false,

			  'name'      => strtolower(date('M', mktime(0,0,0,$i))),

		  ));

	 }

     

      if ( Mage::getSingleton('adminhtml/session')->getWebData() )

      {

          $form->setValues(Mage::getSingleton('adminhtml/session')->getWebData());

          Mage::getSingleton('adminhtml/session')->setCpiboardData(null);

      } elseif ( Mage::registry('cpiboard_data') ) {

          $form->setValues(Mage::registry('cpiboard_data')->getData());

      }

      return parent::_prepareForm();

  }

}