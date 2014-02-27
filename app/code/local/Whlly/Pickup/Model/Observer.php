<?php

class Whlly_Pickup_Model_Observer extends Varien_Object
{
	public function saveShippingMethod($evt){
		$request = $evt->getRequest();
		$quote = $evt->getQuote();
		$pickup = $request->getParam('shipping_pickup',false);
		$quote_id = $quote->getId();
		$data = array($quote_id => $pickup);
		if($pickup){
			Mage::getSingleton('checkout/session')->setPickup($data);
		}
	}
	/*public function saveOrderAfter($evt){
		$order = $evt->getOrder();
		$quote = $evt->getQuote();
		$quote_id = $quote->getId();
		$pickup = Mage::getSingleton('checkout/session')->getPickup();
		if(isset($pickup[$quote_id])){
			$data = $pickup[$quote_id];
			$data['order_id'] = $order->getId();
			$pickupModel = Mage::getModel('pickup/pickup');
			$pickupModel->setData($data);
			$pickupModel->save();
		}
	}*/
	
	public function saveOrderAfter($evt){
		$order = $evt->getOrder();
		$quote = $evt->getQuote();
		$quote_id = $quote->getId();
		$pickup = Mage::getSingleton('checkout/session')->getPickup();
		if(isset($pickup[$quote_id])){
			$data = $pickup[$quote_id];
			$data['order_id'] = $order->getId();
			$store = Mage::getModel('stores/stores')
                ->load($data['store_id']);
			$data['store'] = $store->getStoreName();		
			$data['address'] = $store->getAddress1().'<br />'.$store->getAddress2().'<br /> Tel: '.$store->getTelephone();				
			$pickupModel = Mage::getModel('pickup/pickup');
			$pickupModel->setData($data);
			$pickupModel->save();
		}
	}
	public function loadOrderAfter($evt){
		$order = $evt->getOrder();
		if($order->getId()){
			$order_id = $order->getId();
			$pickupCollection = Mage::getModel('pickup/pickup')->getCollection();
			$pickupCollection->addFieldToFilter('order_id',$order_id);
			$pickup = $pickupCollection->getFirstItem();
			$order->setPickupObject($pickup);
		}
	}	
	public function loadQuoteAfter($evt)
	{
		$quote = $evt->getQuote();
		if($quote->getId()){
			$quote_id = $quote->getId();
			$pickup = Mage::getSingleton('checkout/session')->getPickup();
			if(isset($pickup[$quote_id])){
				$data = $pickup[$quote_id];
				$quote->setPickupData($data);
			}
		}
	}
}