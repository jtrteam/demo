<?php
 
class MDN_PointOfSales_Model_Carrier_StorePickup extends Mage_Shipping_Model_Carrier_Abstract
{
  protected $_code = 'storepickup';
 
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
    	//return false if we are on frontend
    	if (mage::getDesign()->getArea() == 'frontend')
    		return false;

    	// skip if not enabled
	    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
	        return false;
	    }
	 
	    // this object will be returned as result of this method
	    // containing all the shipping rates of this method
	    $result = Mage::getModel('shipping/rate_result');
	    $method = Mage::getModel('shipping/rate_result_method');
	    $method->setCarrier($this->_code);
	    $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
	    $method->setMethod($this->_code);
	    $method->setMethodTitle(mage::helper('PointOfSales')->__('Store pickup'));
	    $method->setCost(0);
	    $method->setPrice(0);
	    $result->append($method);
	 
	    return $result;
	}
	
    public function getAllowedMethods()
    {
        return array('storepickup'=>$this->getConfigData('name'));
    }

}