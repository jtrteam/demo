<?php
class Whlly_Pickup_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function getShippingDescription(){
		$desc = parent::getShippingDescription();
		$pickupObject = $this->getPickupObject();
		if($pickupObject){
			$desc .= '<br/><b>Store</b>: '.$pickupObject->getStore();
			$desc .= '<br/><b>Address</b>: '.$pickupObject->getAddress();
			$desc .= '<br/>';
		}
		return $desc;
	}
}