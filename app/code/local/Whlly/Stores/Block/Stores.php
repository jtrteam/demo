<?php
class Whlly_Stores_Block_Stores extends Mage_Core_Block_Template
{
   
   public function getStoreData(){
	   
	   if($this->getRequest()->getParam('id')):
	   		$id = $this->getRequest()->getParam('id');		
	   elseif(Mage::getStoreConfig('stores/genaral/default_store')):
	   		$id = Mage::getStoreConfig('stores/genaral/default_store');
	   else:
		   $id =0;		
	   endif;
	   	
	   $page = Mage::getModel('stores/stores')
                /*->setStoreId(Mage::app()->getStore()->getId())*/
                ->load($id);				
	   return $page; 
	   
   }
   
    public function getAllStores(){
	   $storesData = Mage::getModel('stores/stores')->getCollection();
	   foreach($storesData as $store):
            $data[]= array( 'id' => $store->getId(), 'name' => $store->getStoreName());
        endforeach;              
        return $data;	   
   }
	
}
