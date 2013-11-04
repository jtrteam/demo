<?php

/*
 * retourne le contenu d'une commande
 */

class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_SelectProduct extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        //collect information
        $product = $row;
        $store = mage::helper('PointOfSales/User')->getStore();

        //check if product is saleable
        if (!$product->isSalable())
                return Mage::helper('PointOfSales')->__('Out of stock');

        //define products prices
        $price = $product->getPrice();
        if ($product->getspecial_price() != '') {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $product->getspecial_from_date(), $product->getspecial_to_date()))
                $price = $product->getspecial_price();
        }

        //define tax rate
        $helper = Mage::getSingleton('tax/calculation');
        $defaultAddress = mage::helper('PointOfSales/User')->getFakeAddress();

        $request = $helper->getRateRequest($defaultAddress, $defaultAddress, false, $store);
        $request->setProductClassId($product->getTaxClassId());
        $taxRate = $helper->getRate($request);

		// apply currency conversion
		$price = Mage::helper('PointOfSales/Currency')->convert($price);
		
        //define prices
        if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
            $priceInclTax = $price;
            $priceExclTax = $priceInclTax / (1 + ($taxRate / 100));
        } else {
            $priceExclTax = $price;
            $priceInclTax = $price * (1 + ($taxRate / 100));
        }

        //format prices
        $priceExclTax = number_format($priceExclTax, 2, '.', '');
        $taxRate = number_format($taxRate, 4, '.', '');
        $priceInclTax = number_format($priceInclTax, 2, '.', '');

        $productName = $this->cleanTxt($row->getname());
        $productId = $row->getId();
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::getStoreConfig('PointOfSales/configuration/default_currency'))->getSymbol();
        $skinUrl = $this->getSkinUrl('images/OrderWizardCreation/');
        $linkCaption = '<img src="' . $this->getSkinUrl('images/OrderWizardCreation/add.png') . '">';

        $retour = '<a href="javascript:selectProduct(' . $productId . ',\'' . $productName . '\', ' . $priceExclTax . ', ' . $taxRate . ', ' . $priceInclTax . ', \'' . $skinUrl . '\',\'' . $currencySymbol . '\')">' . $linkCaption . '</a>';

        return $retour;
    }

    public function cleanTxt($txt) {
        return addslashes(str_replace('"', ' ', $txt));
    }

}