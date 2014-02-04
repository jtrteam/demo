<?php
class Whlly_Stores_Model_Source_Store extends Varien_Object
{
    public function toOptionArray()
    {
        $storesData = Mage::getModel('stores/stores')->getCollection();
		$data =array();		
		foreach($storesData as $store):
            $data[]= array( 'value' => $store->getId(), 'label' => $store->getStoreName());
        endforeach;              
        return $data;
        
    }
}