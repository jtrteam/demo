<?php

class MDN_PointOfSales_Block_SalesReport_OrderInfo extends Mage_Core_Block_Template
{
	private $_order = null;
	
	public function setOrderId($orderId)
	{
		$this->_order = mage::getModel('sales/order')->load($orderId);
	}
	
	public function getOrder()
	{
		return $this->_order;
	}
}