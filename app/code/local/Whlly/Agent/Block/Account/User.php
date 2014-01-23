<?php

class Whlly_Agent_Block_Account_User extends Mage_Core_Block_Template
{
    protected $_subscription = null;

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getAccountUrl()
    {
        return Mage::getUrl('customer/account/edit', array('_secure'=>true));
    }

    public function getAddressesUrl()
    {
        return Mage::getUrl('customer/address/index', array('_secure'=>true));
    }

    public function getAddressEditUrl($address)
    {
        return Mage::getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    public function getOrdersUrl()
    {
        return Mage::getUrl('customer/order/index', array('_secure'=>true));
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/customer/index', array('_secure'=>true));
    }

    public function getWishlistUrl()
    {
        return Mage::getUrl('customer/wishlist/index', array('_secure'=>true));
    }

    public function getTagsUrl()
    {

    }

    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($this->getCustomer());
        }

        return $this->_subscription;
    }

    public function getManageNewsletterUrl()
    {
        return $this->getUrl('*/newsletter/manage');
    }

    public function getSubscriptionText()
    {
        if($this->getSubscriptionObject()->isSubscribed()) {
            return Mage::helper('customer')->__('You are currently subscribed to our newsletter.');
        }

        return Mage::helper('customer')->__('You are currently not subscribed to our newsletter.');
    }

    public function getPrimaryAddresses()
    {
        $addresses = $this->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return $addresses;
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Mage_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Mage_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }
	
	public function getMyCustomers()
	{
		
		$collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
			->addAttributeToFilter('membership', 0)
			->addAttributeToFilter('group_id',Mage::getSingleton('customer/session')->getCustomerGroupId());

        
        return $collection;
	}
}
