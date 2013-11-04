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
class MDN_PointOfSales_Helper_User extends Mage_Core_Helper_Abstract
{
	private $_currency = null;
	private $_defaultRegion = null;
	
	/**
	 * Return currency associated to user
	 *
	 */
	public function getCurrency()
	{
		if ($this->_currency == null)
		{
			$currencyCode = Mage::getSingleton('admin/session')->getUser()->getcurrency();
			$this->_currency = Mage::getModel('directory/currency')->load($currencyCode);
		}
		return $this->_currency;
	}

	/**
	 * return default shipping method
	 *
	 * @return unknown
	 */
	public function getDefaultShippingMethod()
	{
		return Mage::getSingleton('admin/session')->getUser()->getdefault_shipping_method();
	}
	
	/**
	 * return default payment method
	 *
	 * @return unknown
	 */
	public function getDefaultPaymentMethod()
	{
		return Mage::getSingleton('admin/session')->getUser()->getdefault_payment_method();		
	}
	
	/**
	 * return customer group
	 *
	 * @return unknown
	 */
	public function getCustomerGroup()
	{
		return Mage::getSingleton('admin/session')->getUser()->getcustomer_group();	
	}
	
	/**
	 * return default country
	 *
	 * @return unknown
	 */
	public function getDefaultCountryId()
	{
		return Mage::getSingleton('admin/session')->getUser()->getcountry_id();
	}
	
	public function getDefaultCountry()
	{
		return mage::getModel('directory/country')->load($this->getDefaultCountryId());
	}
	
	public function getDefaultCity()
	{
		return Mage::getSingleton('admin/session')->getUser()->getcity();
	}
	
	public function getDefaultZip()
	{
		return Mage::getSingleton('admin/session')->getUser()->getpostcode();
	}
	
	/**
	 * return default region
	 *
	 */
	public function getDefaultRegion()
	{
		if ($this->_defaultRegion == null)
		{
			$regionCode = Mage::getSingleton('admin/session')->getUser()->getregion();
			
			$country = $this->getDefaultCountry();
			foreach ($country->getRegions() as $item)
			{
				if ($item->getcode() == $regionCode)
				{
					$this->_defaultRegion = $item;
					return $this->_defaultRegion;
				}
			}
		}
				
		return $this->_defaultRegion;
	}
	
	/**
	 * return store id
	 *
	 * @return unknown
	 */
	public function getStoreId()
	{
		return Mage::getSingleton('admin/session')->getUser()->getstore_id();	
	}
	
	/**
	 * Return store
	 *
	 * @return unknown
	 */
	public function getStore()
	{
		return mage::getModel('core/store')->load($this->getStoreId());
	}
	
	/**
	 * Return website id
	 *
	 */
	public function getWebsiteId()
	{
		return $this->getStore()->getWebsiteId();
	}
	
	/**
	 * Return product price for POS incl tax
	 *
	 */
	public function getPosProductPriceInclTax($product, $price)
	{
		//init vars
		$helper = mage::helper('tax');
		$customerGroup = mage::getModel('customer/group')->load($this->getCustomerGroup());
		$CustomerTaxClass = $customerGroup->gettax_class_id();
		$store = $this->getStoreId();
		
		//create fake addresses
		$Address = $this->getFakeAddress();
		$priceInclTax = $helper->getPrice($product, $price, true, $Address, $Address, $CustomerTaxClass, $store);
		
		return $priceInclTax;
	}
		
	/**
	 * Return product price for POS
	 *
	 */
	public function getPriceExclTaxFromPriceInclTax($product, $priceInclTax)
	{
		//init vars
		$helper = mage::helper('tax');
		$customerGroup = mage::getModel('customer/group')->load($this->getCustomerGroup());
		$CustomerTaxClass = $customerGroup->gettax_class_id();
		$storeId = $this->getStoreId();
		$store = mage::getModel('core/store')->load($storeId);
		$taxClassId = $product->getTaxClassId();
		        
		//create fake addresses
		$Address = $this->getFakeAddress();
		
		//retrieve tax percent
		$request = Mage::getSingleton('tax/calculation')->getRateRequest($Address, $Address, $CustomerTaxClass, $store);
        $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
        	
        //compute price excl tax from tax rate
        $priceExclTax = $priceInclTax * 100 / (100 + $percent);
        $priceExclTax = number_format($priceExclTax, 4, '.', '');
        
		return $priceExclTax;
	}
	
	/**
	 * Return shipping tax rate
	 *
	 */
	public function getShippingTaxRate()
	{
		$store = mage::helper('PointOfSales/User')->getStore();
		$shippingTaxClassId = mage::getStoreConfig('tax/classes/shipping_tax_class');
		$Address = $this->getFakeAddress();
		$customerGroup = mage::getModel('customer/group')->load($this->getCustomerGroup());
		$CustomerTaxClass = $customerGroup->gettax_class_id();
		$request = Mage::getSingleton('tax/calculation')->getRateRequest($Address, $Address, $CustomerTaxClass, $store);
        $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($shippingTaxClassId));
		
		return $percent;
	}
	 
	 
	/**
	 * create a fake address matching to user settings
	 *
	 * @return unknown
	 */
	public function getFakeAddress()
	{
		$address = Mage::getModel('sales/order_address');
		$address->setId(null);
		$address->setcountry_id($this->getDefaultCountryId());
		$address->setcity($this->getDefaultCity());
		$address->setpostcode($this->getDefaultZip());
		
		if ($this->getDefaultRegion())
			$address->setregion($this->getDefaultRegion()->getId());
		
		return $address;
		
		
	}
	
}
