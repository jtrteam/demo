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
class MDN_PointOfSales_Helper_SalesOrderPostData extends Mage_Core_Helper_Abstract {

    /**
     * Create an array to simulate magento sales order form post data (and then use the same controllers functions)
     *
     * @param unknown_type $object
     */
    public function convert($object) {
        $data = array();

        //customer
        $customer = null;
        if ($object['customer_id']) {
            $customerId = $object['customer_id'];
            $customer = mage::getModel('customer/customer')->load($customerId);
            $data['name'] = $customer->getName();
            $data['email'] = $customer->getemail();
        } else {
            $data['name'] = $object['customer_firstname'];
            $data['email'] = $object['customer_email'];
        }
        $data['telephone'] = $object['phone'];

        //billing address
        $data['billing_postcode'] = '';
        $data['billing_country_id'] = '';
        $data['billing_region'] = '';
        $data['store_name'] = '';
        $data['store_id'] = mage::helper('PointOfSales/User')->getStoreId();

        //order
        $data['order'] = array();
        $data['order']['store_id'] = mage::helper('PointOfSales/User')->getStoreId();
        $data['order']['currency'] = mage::helper('PointOfSales/User')->getCurrency()->getCode();
        $data['order']['account'] = array();
        $data['order']['account']['group_id'] = mage::helper('PointOfSales/User')->getCustomerGroup();
        $data['order']['account']['email'] = $data['email'];

        //billing address
        $data['order']['billing_address'] = array();
        $data['order']['billing_address']['customer_address_id'] = '';
        $data['order']['billing_address']['prefix'] = '';

        if ($customer == null) {
            $data['order']['billing_address']['firstname'] = $object['customer_firstname'];
            $data['order']['billing_address']['middlename'] = '';
            $data['order']['billing_address']['lastname'] = $object['customer_lastname'];
            $data['order']['billing_address']['suffix'] = '';
        } else {
            $data['order']['billing_address']['firstname'] = $customer->getfirstname();
            $data['order']['billing_address']['middlename'] = $customer->getmiddlename();
            $data['order']['billing_address']['lastname'] = $customer->getlastname();
            $data['order']['billing_address']['suffix'] = $customer->getsuffix();
        }

        $data['order']['billing_address']['company'] = $object['customer_company'];
        $data['order']['billing_address']['street'] = array($object['address']);
        $data['order']['billing_address']['city'] = $object['city'];
        $data['order']['billing_address']['country_id'] = $object['country'];
        $data['order']['billing_address']['region'] = '';
        if (isset($object['region']) && $object['region'] != '') {
            $data['order']['billing_address']['region_id'] = $object['region'];
        } elseif (mage::helper('PointOfSales/User')->getDefaultRegion()) {
            $data['order']['billing_address']['region_id'] = mage::helper('PointOfSales/User')->getDefaultRegion()->getId();
        }
        $data['order']['billing_address']['postcode'] = $object['zip'];
        $data['order']['billing_address']['telephone'] = $object['phone'];
        $data['order']['billing_address']['fax'] = $object['fax'];

        //shipping address
        $data['order']['shipping_address'] = $data['order']['billing_address'];

        //shipping method
        $data['order']['shipping_method'] = array();
        $data['order']['shipping_method'] = $object['shipping_method'];

        //payment method
        $data['order']['payment'] = array('method' => $object['payment_method']);
        $data['order']['payment_method'] = $object['payment_method'];

        //misc
        $data['order']['sku'] = '';
        $data['order']['price'] = array('from' => '', 'to' => '');
        $data['order']['shipping_same_as_billing'] = 'on';
        $data['order']['coupon_code'] = '';

        //products
        $data['order']['add_products'] = '';
        foreach ($object['products'] as $productInfoSrc) {
            $productId = $productInfoSrc['product_id'];
            $qty = $productInfoSrc['qty'];
            $data['order']['add_products'][$productId] = array('qty' => $qty);
        }

        return $data;
    }

}