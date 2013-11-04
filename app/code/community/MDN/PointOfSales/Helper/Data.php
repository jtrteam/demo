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
class MDN_PointOfSales_Helper_Data extends Mage_Core_Helper_Abstract {

    private $_anonymousCustomer = null;

    /**
     * Init quote
     *
     * @param unknown_type $object
     */
    protected function init($object) {
        //convert object to the magento way
        $post = mage::helper('PointOfSales/SalesOrderPostData')->convert($object);

        //init session
        $this->_getSession()->clear();

        switch ($object['customer_mode']) {
            case 'guest':
                $this->_getOrderCreateModel()->getQuote()->setCustomerIsGuest(true);
                $this->_getSession()->setCustomerId(-1);
                break;
            case 'new':
                if ($object['customer_id'] > 0) {
                    $this->_getOrderCreateModel()->getQuote()->setCustomerIsGuest(false);
                    $this->_getSession()->setCustomerId($object['customer_id']);
                } else {
                    $this->_getOrderCreateModel()->getQuote()->setCustomerIsGuest(true);
                    $this->_getSession()->setCustomerId(-1);
                }
                break;
            default:
                $this->_getOrderCreateModel()->getQuote()->setCustomerIsGuest(false);
                $this->_getSession()->setCustomerId($object['customer_id']);
                break;
        }

        $this->_getSession()->setStoreId(mage::helper('PointOfSales/User')->getStoreId());
        $this->_getSession()->setCurrencyId(mage::helper('PointOfSales/User')->getCurrency()->getId());
        $this->_getOrderCreateModel()->setRecollect(true);
        $this->_getOrderCreateModel()->getBillingAddress();

        $this->_getOrderCreateModel()->getQuote()->setStoreId(mage::helper('PointOfSales/User')->getStoreId());

        $this->_getOrderCreateModel()->getQuote()->setBaseCurrencyCode(mage::helper('PointOfSales/User')->getCurrency()->getId());
        $this->_getOrderCreateModel()->getQuote()->setQuoteCurrencyCode(mage::helper('PointOfSales/User')->getCurrency()->getId());
        $this->_getOrderCreateModel()->getQuote()->setStoreCurrencyCode(mage::helper('PointOfSales/User')->getCurrency()->getId());
        $this->_getOrderCreateModel()->getQuote()->setGlobalCurrencyCode(mage::helper('PointOfSales/User')->getCurrency()->getId());

        //add products
        $this->_getOrderCreateModel()->resetShippingMethod(true);
        if (is_array($post['order']['add_products']))
            $this->_getOrderCreateModel()->addProducts($post['order']['add_products']);

        $this->_getOrderCreateModel()
                ->saveQuote();

        //apply custom product price
        $productsData = array();
        foreach ($this->_getOrderCreateModel()->getQuote()->getItemsCollection() as $item) {
            $productId = $item->getproduct_id();
            foreach ($object['products'] as $srcProduct) {
                if ($productId == $srcProduct['product_id']) {
                    if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
                        $item->setCustomPrice($srcProduct['priceInclTax']);
                        $item->setOriginalCustomPrice($srcProduct['priceInclTax']);
                    } else {
                        $item->setCustomPrice($srcProduct['priceExclTax']);
                        $item->setOriginalCustomPrice($srcProduct['priceExclTax']);
                    }
                }
            }
        }

        //addresses
        $this->_getOrderCreateModel()->setShippingAsBilling(1);
        $this->_getOrderCreateModel()->importPostData($post['order']);

        //payment
        if (isset($post['payment']))
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($post['payment']);
        if ($this->_getOrderCreateModel()->getQuote()->getPayment())
            $this->_getOrderCreateModel()->getQuote()->getPayment()->setQuote($this->_getOrderCreateModel()->getQuote());

        $method = $post['order']['shipping_method'];
        $this->_getOrderCreateModel()->collectShippingRates();

        $eventData = array(
            'order_create_model' => $this->_getOrderCreateModel(),
            'request' => $post,
        );

        Mage::dispatchEvent('adminhtml_sales_order_create_process_data', $eventData);

        $this->_getOrderCreateModel()
                ->initRuleData()
                ->saveQuote();

        if (isset($post['payment'])) {
            if ($paymentData = $post['payment']) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }
        }

        $this->_getOrderCreateModel()
                ->saveQuote();

        //force shipping method using the first rate
        $shippingMethod = null;
        $this->_getOrderCreateModel()->getQuote()->getShippingAddress()->collectShippingRates();
        $this->_getOrderCreateModel()->collectShippingRates();
        foreach ($this->_getOrderCreateModel()->getQuote()->getShippingAddress()->getShippingRatesCollection() as $rate) {
            $shippingMethod = $rate->getcode();
            break;
        }

        $this->_getOrderCreateModel()->setShippingMethod($shippingMethod);

        $this->_getOrderCreateModel()->saveQuote();
    }

    /**
     * Create an order and return it
     *
     * @param unknown_type $object
     */
    public function createOrder($object) {
        $this->init($object);

        //try to validated payment object
        $methodInstance = $this->_getOrderCreateModel()->getQuote()->getPayment()->getMethodInstance();
        $methodInstance->getInfoInstance()->setQuote($this->_getOrderCreateModel()->getQuote());
        $methodInstance->validate();

        //Assumes that we use the same currency
        //Todo: implement magento logic (if base currency differs from sales currency
        $this->_getOrderCreateModel()->getQuote()->setStoreToQuoteRate(1);
        $this->_getOrderCreateModel()->getQuote()->setBaseToGlobalRate(1);
        $this->_getOrderCreateModel()->getQuote()->setStoreToBaseRate(1);
        $this->_getOrderCreateModel()->getQuote()->setBaseToQuoteRate(1);

        $this->_getOrderCreateModel()->saveQuote();

        $order = $this->_getOrderCreateModel()->createOrder();

        //add order comments
        if ($object['comments'] != '') {
            $order->addStatusToHistory($order->getStatus(), $object['comments'], false);
            $order->save();
        }

        //inactivate quote (to avoir that it's displayed in abandonned cart report
        $this->_getOrderCreateModel()->getQuote()->setIsActive(false)->save();

        return $order;
    }

    /**
     * Return shipping rates
     *
     * @param unknown_type $object
     */
    public function getShippingRates($object) {
        $this->init($object);

        $addedRates = array();

        $shippingRates = array();
        foreach ($this->_getOrderCreateModel()->getQuote()->getShippingAddress()->getShippingRatesCollection() as $rate) {
		
            $price = $rate->getprice();
			
			//apply currency rate
			$price = Mage::Helper('PointOfSales/Currency')->convert($price);

            //if shipping prices doesn't include tax, add them
            if (Mage::getStoreConfig('tax/calculation/shipping_includes_tax') != 1) {
                $taxRate = mage::helper('PointOfSales/User')->getShippingTaxRate();
                $price = $price * (1 + ($taxRate / 100));
            }

            $currency = mage::helper('PointOfSales/User')->getCurrency();

            //hack to avoid to add the same method twice
            $key = $rate->getcarrier() . '_' . $rate->getmethod();
            if (in_array($key, $addedRates))
                continue;

            $shippingRates[] = array('code' => $rate->getcode(),
                'price' => $price,
                'price_formated' => $currency->format($price, array(), false),
                'label' => $rate->getcarrier_title() . ' - ' . $rate->getmethod_title(),
                'method' => $rate->getmethod(),
                'carrier' => $rate->getcarrier());

            $addedRates[] = $key;
        }

        return $shippingRates;
    }

    /**
     * return address (shipping & billing are the same)
     *
     */
    private function getAddress($object, $order, $customer) {
        $addressEntityTypeId = mage::helper('PointOfSales/MagentoVersionCompatibility')->getSalesOrderAddressEntityId();

        $address = Mage::getModel('sales/order_address');
        //load address data if set
        if (isset($object['address_id']) && ($object['address_id'] != '')) {
            $customer_shipping_address = Mage::getModel('customer/address')->load($object['address_id']);
            $address->setData($customer_shipping_address->getData());
        }
        $address->setOrder($order);
        $address->setId(null);
        $address->setentity_type_id($addressEntityTypeId);
        if ($customer) {
            $address->setFirstname($customer->getFirstname());
            $address->setLastname($customer->getlastname());
        }
        $address->setcompany($object['customer_company']);
        $address->setStreet($object['address']);
        $address->setCity($object['city']);
        $address->setPostcode($object['zip']);
        $address->setcountry_id($object['country']);
        $address->setEmail($object['customer_email']);
        $address->setTelephone($object['phone']);
        $address->setmobile($object['mobile']);
        $address->setfax($object['fax']);

        if ($object['customer_email'] != '')
            $address->setEmail($object['customer_email']);

        return $address;
    }

    //**************************************************************************************************************************
    //**************************************************************************************************************************
    //Create shipment and invoice
    //**************************************************************************************************************************
    //**************************************************************************************************************************

    /**
     * Create invoice
     *
     */
    public function createInvoice($new_order, $comments = '') {
        $convertor = Mage::getModel('sales/convert_order');
        $invoice = $convertor->toInvoice($new_order);

        //Browse order items
        foreach ($new_order->getAllItems() as $orderItem) {
            //ajout au invoice
            $InvoiceItem = $convertor->itemToInvoiceItem($orderItem);
            $InvoiceItem->setQty($orderItem->getqty_ordered());
            $invoice->addItem($InvoiceItem);
        }

        //add comments
        if ($comments != '')
            $invoice->addComment($comments, false);

        //Save invoice
        $invoice->collectTotals();
        $invoice->register();

        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
        $invoice->pay();
        $invoice->save();

        //Payment method
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('banktransfer');
        $payment->setOrder($new_order);
        $new_order->addPayment($payment);
        $payment->pay($invoice);
        $payment->save();


        return $invoice;
    }

    /**
     * Create shipment
     *
     */
    public function createShipment($new_order, $data) {
        if (!$this->createShipmentNeeded($data))
            return false;

        $convertor = Mage::getModel('sales/convert_order');
        $shipment = $convertor->toShipment($new_order);

        foreach ($new_order->getAllItems() as $orderItem) {
            if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            if (!$this->productIsShipped($orderItem->getproduct_id(), $data))
                continue;

            $ShipmentItem = $convertor->itemToShipmentItem($orderItem);
            $ShipmentItem->setQty($orderItem->getqty_ordered());
            $shipment->addItem($ShipmentItem);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
    }

    //*******************************************************************************************************************************************
    //*******************************************************************************************************************************************
    // Tools
    //*******************************************************************************************************************************************
    //*******************************************************************************************************************************************

    /**
     * Return true if we need to create a shipment
     *
     * @param unknown_type $data
     */
    protected function createShipmentNeeded($data) {
        foreach ($data['products'] as $product) {
            if ($product['shipped'] == 1)
                return true;
        }
        return false;
    }

    protected function productIsShipped($productId, $data) {
        foreach ($data['products'] as $product) {
            if ($product['product_id'] == $productId) {
                return ($product['shipped']);
            }
        }
        return false;
    }

    /**
     * Return anonymous customer
     *
     * @return unknown
     */
    public function getAnonymousCustomer() {
        if ($this->_anonymousCustomer == null) {
            $customerId = Mage::getSingleton('admin/session')->getUser()->getcustomer_anonymous_id();
            $this->_anonymousCustomer = mage::getModel('customer/customer')->load($customerId);
            $firstname = $this->_anonymousCustomer->getFirstname();
            if (empty($firstname))
                throw new Exception('Need to create an anonymous customer and link to your account Or Need to complete a field customer to create one');
        }
        return $this->_anonymousCustomer;
    }

    /**
     * Retrieve order create model
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel() {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    protected function _getSession() {
        return Mage::getSingleton('adminhtml/session_quote');
    }

}

?>