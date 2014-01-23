<?php
class Whlly_Agent_Block_Account extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('agent/customer.phtml');
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('agent')->__('Customers'));
    }
	
	
}
