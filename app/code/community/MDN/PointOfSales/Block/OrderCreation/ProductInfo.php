<?php

class MDN_PointOfSales_Block_OrderCreation_ProductInfo extends Mage_Core_Block_Template
{
    private $_product = null;

    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function getMargin()
    {
        $price = $this->getProduct()->getPrice();
        if ($price > 0)
        {
            $cost = $this->getProduct()->getCost();
            $margin = (($price - $cost) / $price) * 100;
        }
        else
            $margin = '?';
        return $margin;
    }

    public function getProductImageUrl(){
        $baseUrl = Mage::getBaseUrl('media');
        return $baseUrl.'catalog/product'.$this->getProduct()->getimage();
    }

    public function getProductShelf(){
        return Mage::helper('PointOfSales/ShelfLocation')->getShelfLocationForProduct($this->_product);
    }
   
}