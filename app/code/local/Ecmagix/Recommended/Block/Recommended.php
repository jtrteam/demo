<?php
class Ecmagix_Recommended_Block_Recommended extends Mage_Catalog_Block_Product_List
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getRecommendedProducts()     
     { 
        $orders = Mage::getResourceModel('sales/order_collection')
				  ->addFieldToSelect('entity_id')
				  ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
				  ->addAttributeToSort('created_at', 'DESC')
				  ->setPageSize(1);
		$orderId = $orders->getFirstItem()->getId();
		if($orderId){
			$orderItem = Mage::getModel('sales/order')->load($orderId);
            $items = $orderItem->getAllItems();
			foreach ($items as $itemId => $item)
            {
				$productId=$item->getProductId();
			}
			 $products = Mage::getModel('catalog/product')->load($productId);
			 $cats = Mage::getResourceSingleton('catalog/product')->getCategoryIds($products);
			 $category   = Mage::getModel('catalog/category')->load($cats[0]);
			 $storeId    = Mage::app()->getStore()->getId();  
			 $visibility = array(  
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,  
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG  
			);  
 
			$productCollection   = Mage::getModel('catalog/product')->setStoreId($storeId)  
								    ->getCollection()  
									->addAttributeToFilter('visibility', $visibility)  
									->addCategoryFilter($category)  
									->addAttributeToSelect('*') 
									->setOrder('name', 'asc');
	 
			$this->_collection = $productCollection;  
			return $this->_collection; 
		}
        
    }
}