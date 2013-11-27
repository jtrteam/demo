<?php
class Offshorent_Socialaccounts_Block_Socialaccounts extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
    protected function getCustomerFromSession(){
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function __construct() {
        parent::__construct();
        $customer = $this->getCustomerFromSession();
        if($customer){
            $customerObj = Mage::getModel('customer/customer')->load($customer->getId());
            if($facebook = $customerObj->getFacebook()){
                $this->_facebook = $facebook; 
            }else{
                $this->_facebook = null;     
            }
			if($twitter = $customerObj->getTwitter()){
                $this->_twitter = $twitter; 
            }else{
                $this->_twitter = null;     
            }
			if($pinterest = $customerObj->getPinterest()){
                $this->_pinterest = $pinterest; 
            }else{
                $this->_pinterest = null;     
            }
			if($instagram = $customerObj->getInstagram()){
                $this->_instagram = $instagram; 
            }else{
                $this->_instagram = null;     
            }
			if($googleplus = $customerObj->getGoogleplus()){
                $this->_googleplus = $googleplus; 
            }else{
                $this->_googleplus = null;     
            }
        }
        
    }
	
	public function getFacebook(){
        return $this->_facebook;
    }
	public function getFacebookLink()
	{
        $name = $this->getFacebook();
		 return $name;
    }
	
	public function getTwitter(){
        return $this->_twitter;
    }
	public function getTwitterLink()
	{
        $name = $this->getTwitter();
		 return $name;
    }
	
	public function getPinterest(){
        return $this->_pinterest;
    }
	public function getPinterestLink()
	{
        $name = $this->getPinterest();
		 return $name;
    }
	
	public function getInstagram(){
        return $this->_instagram;
    }
	public function getInstagramLink()
	{
        $name = $this->getInstagram();
		 return $name;
    }
	
	public function getGoogleplus(){
        return $this->_googleplus;
    }
	public function getGoogleplusLink()
	{
        $name = $this->getGoogleplus();
		 return $name;
    }
}