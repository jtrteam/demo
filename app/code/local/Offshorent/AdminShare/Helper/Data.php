<?php

class Offshorent_AdminShare_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getStoreId()
	{
		return Mage::getSingleton('admin/session')->getUser()->getstore_id();	
	}
	
	
	public function getStore()
	{
		return mage::getModel('core/store')->load($this->getStoreId());
	}
	
	public function getWebsiteId()
	{
		return $this->getStore()->getWebsiteId();
	}
	
}