<?php
class Whlly_Pickup_Block_Pickup extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
	public function __construct(){
		$this->setTemplate('pickup/pickup.phtml');		
	}
	
	 public function getAllStores(){
	   $storesData = Mage::getModel('stores/stores')->getCollection();
	   foreach($storesData as $store):
            $data[]= array( 'id' => $store->getId(), 'name' => $store->getStoreName());
        endforeach;              
        return $data;	   
   }
}