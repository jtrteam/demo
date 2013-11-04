<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms page edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class MDN_PointOfSales_Block_Adminhtml_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main
{

    protected function _prepareForm()
    {
    	parent::_prepareForm();
    	
    	        
        //Add fieldset with POS settings
        $form = $this->getForm();
        $fieldset = $form->addFieldset('info_caisse', array('legend'=>Mage::helper('adminhtml')->__('POS Information')));     
        $fieldset->addField('store_id', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Store'),
                'required'  => false,
                'name'      => 'store_id',
                'id'      => 'store_id',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ));
        
        $fieldset->addField('country_id', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Default Country'),
                'required'  => false,
                'name'      => 'country_id',
                'id'      => 'country_id',
                'values'    =>  $this->getCountryForSelect(),
            ));
        
        $fieldset->addField('region', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Default Region'),
                'required'  => false,
                'name'      => 'region',
                'id'      => 'region',
                'values'    =>  $this->getRegionForSelect(),
        ));
		
        $fieldset->addField('city', 'text', array(
                'label'     => Mage::helper('PointOfSales')->__('Default City'),
                'required'  => false,
                'name'      => 'city',
                'id'      => 'city'
        ));
            
        $fieldset->addField('postcode', 'text', array(
                'label'     => Mage::helper('PointOfSales')->__('Default Postcode'),
                'required'  => false,
                'name'      => 'postcode',
                'id'      => 'postcode'
        ));
			
       $fieldset->addField('customer_group', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Customers group'),
                'required'  => false,
                'name'      => 'customer_group',
                'id'      => 'customer_group',
                'values'    =>  $this->getCustomerGroupIdForSelect()
        ));
            
        $fieldset->addField('default_payment_method', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Default Payment Method'),
                'required'  => false,
	             'name'      => 'default_payment_method',
                'id'      => 'default_payment_method',
                'values'    =>  mage::getModel('PointOfSales/System_Config_PaymentMethods')->getPosMethods()
        ));
                    
        $fieldset->addField('default_shipping_method', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Default Shipping Method'),
                'required'  => false,
                'name'      => 'default_shipping_method',
                'id'      => 'default_shipping_method',
                'values'    =>  mage::getModel('PointOfSales/System_Config_ShippingMethods')->getAllOptions()
        ));
                            
        $fieldset->addField('currency', 'select', array(
                'label'     => Mage::helper('PointOfSales')->__('Currency'),
                'required'  => false,
                'name'      => 'currency',
                'id'      => 'currency',
                'values'    => mage::getModel('adminhtml/system_config_source_currency')->toOptionArray(false)
        ));
        
        $model = Mage::registry('permissions_user');
        $data = $model->getData();
        unset($data['password']);
        $form->setValues($data);
        
        return $this;
    }
    
    /**
     * Return country list
     *
     * @return unknown
     */
    private function getCountryForSelect(){
     	
      $countryCollection = Mage::getModel('directory/country_api')->items(); // Retrieves country collection
 	  $countryList = array();
	  foreach($countryCollection as $country) { // Build the array to inject into select
		   $countryList[$country['country_id']]['value'] = $country['country_id'];
		   $countryList[$country['country_id']]['label'] = $country['name'];
	  }

	  return $countryList;
    }
    
    /**
     * Return country list
     *
     * @return unknown
     */
    private function getRegionForSelect(){

      $regionCollection = Mage::getModel('directory/region')->getCollection(); // Retrieves country collection
 	  $regionList = array();
	  
	  //add empty item
	  $regionList['']['value'] = '';
	  $regionList['']['label'] = '';
	  
	  foreach($regionCollection as $region) { // Build the array to inject into select
                  $countryCode = $region->getcountry_id();
                  $country = mage::getModel('directory/country')->loadByCode($countryCode);
		   $regionList[$region['region_id']]['value'] = $region['code'];
		   $regionList[$region['region_id']]['label'] = $country->getName().' - '.$region['default_name'];
	  }
	  

	  return $regionList;
    }

    /**
     * Return customer groups
     *
     * @return unknown
     */
    private function getCustomerGroupIdForSelect(){
    	
    	$groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
            
        return $groups;
    }
    
}
