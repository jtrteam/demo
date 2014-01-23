<?php
require_once 'Mage/Customer/controllers/AccountController.php';
class Whlly_Agent_CustomerController extends Mage_Customer_AccountController
{
   
    public function preDispatch()
    {
        parent::preDispatch();
        $membership = Mage::getSingleton('customer/session')->getCustomer()->getMembership();
		if( ($membership == 1) && (Mage::getStoreConfig('agent/genaral/enable')) ):			
			$this->_redirectUrl($this->_getUrl('agent/account'));		
		endif;
    }
   
}
