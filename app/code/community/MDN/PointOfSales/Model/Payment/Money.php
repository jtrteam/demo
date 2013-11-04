<?php

class MDN_PointOfSales_Model_Payment_Money extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'Money';
    protected $_formBlockType = 'PointOfSales/Payment_Form';
    protected $_infoBlockType = 'PointOfSales/Payment_Info';

    protected $_canUseCheckout = false;

        
    public function getInformation() {
        return $this->getConfigData('information');
    }

    public function getAddress() {
        return $this->getConfigData('address');
    }

    public function getCode()
    {
        return $this->_code;
    }
    
}