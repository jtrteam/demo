<?php

/*
* retourne le contenu d'une commande
*/
class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductPrice
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	
    	//collect information
        $store = mage::helper('PointOfSales/User')->getStore();
    	$product = $row;
    	$price = $product->getPrice();
    	if ($product->getspecial_price() != '')
        {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $product->getspecial_from_date(), $product->getspecial_to_date()))
    		$price = $product->getspecial_price();
        }
    	
    	
    	//define tax rate
    	$helper = Mage::getSingleton('tax/calculation');
    	$defaultAddress = mage::helper('PointOfSales/User')->getFakeAddress();
		$request = $helper->getRateRequest($defaultAddress, $defaultAddress, false, $store);
		$request->setProductClassId($product->getTaxClassId());
        $taxRate = $helper->getRate($request);

        //define price
		if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1)
			$priceInclTax = $price;
		else 
			$priceInclTax = $price * (1 + ($taxRate / 100));

        //format & return
        $currency = mage::helper('PointOfSales/User')->getCurrency();
		
		$value = Mage::helper('PointOfSales/Currency')->convert($priceInclTax);
        $value = number_format($value, 2);
        return $currency->format($value, array(), false);
    }

}