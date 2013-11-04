<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_PointOfSales_Helper_Customer extends Mage_Core_Helper_Abstract {

    /**
     * Method that create and return customer
     * If customer email already used, only return customer
     *
     * @param unknown_type $data
     */
    public function createCustomer($data) {
        //try to load by customer by email
        $customer = Mage::getModel('customer/customer')
                        ->setWebsiteId(mage::helper('PointOfSales/User')->getWebsiteId())
                        ->loadByEmail($data['customer_email']);

        //create customer if doesn't exist
        if (!$customer->getId()) {
            $customer = mage::getModel('customer/customer')
                            ->setfirstname($data['customer_firstname'])
                            ->setlastname($data['customer_lastname'])
                            ->setemail($data['customer_email'])
                            ->setstore_id(mage::helper('PointOfSales/User')->getStoreId())
                            ->setgroup_id(mage::helper('PointOfSales/User')->getCustomerGroup())
                            ->setwebsite_id(mage::helper('PointOfSales/User')->getWebsiteId())
                            ->save();

        } else {
            throw new Exception($this->__('Customer email already used !'));
        }

        return $customer;
    }

    /**
     * Add customer to newsletter subscription
     */
    public function newsletterSubscription($id){

        $customer = Mage::getModel('customer/customer')->load($id);

        $newsletter = Mage::getModel('newsletter/subscriber')->getCollection()
                ->addFieldToFilter('subscriber_email', $customer->getemail());

        if($newsletter->count() == 0){

            $newsletter = Mage::getModel('newsletter/subscriber')
                            ->setcustomer_id($customer->getid())
                            ->setstore_id(mage::helper('PointOfSales/User')->getStoreId())
                            ->setsubscriber_email($customer->getemail())
                            ->setsubscriber_status(1)
                            ->save();

        }

    }

    /**
     * Create customer address
     * @param <type> $data
     */
    public function createAddress($data, $customerId)
    {
        
        $regionName = mage::getModel('directory/region')->load($data['region'])->getName();

        //create address
        $model = mage::getModel('customer/address')
                    ->setCustomerId($customerId)
                    ->setfirstname($data['customer_firstname'])
                    ->setlastname($data['customer_lastname'])
                    ->setcountry_id($data['country'])
                    ->setregion($regionName)
                    ->setregionid($data['region'])
                    ->setpostcode($data['zip'])
                    ->setcity($data['city'])
                    ->setstreet($data['address'])
                    ->settelephone($data['phone'])
                    ->setfax($data['fax'])
                    ->setcompany($data['customer_company'])
                    ->setIsDefaultShipping()
                    ->setIsDefaultBilling()
                    ->save();

        $customer = mage::getModel('customer/customer')->load($customerId);
        $customer->setDefaultBilling($model->getId());
        $customer->setDefaultShipping($model->getId());
        $customer->save();

    }
    
}