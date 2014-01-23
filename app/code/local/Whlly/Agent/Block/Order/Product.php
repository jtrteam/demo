<?php

class Whlly_Agent_Block_Order_Product extends Mage_Catalog_Block_Product_List
{

	 public function getStoreCategories()
    {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }
   
}
