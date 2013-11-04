<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_PointOfSales_Helper_Barcode extends Mage_Core_Helper_Abstract
{
    public function getBarcodeForProduct($product)
    {
        $barcodeAttributeCode = mage::getStoreConfig('pointofsales/barcode_scanner/barcode_attribute');
        if ($barcodeAttributeCode == '')
                throw new Exception($this->__('Barcode attribute not set in Sytem > Configuration > Point of sales'));

        return $product->getData($barcodeAttributeCode);
        
    }

    public function getProductFromBarcode($barcode)
    {
        $barcodeAttributeCode = mage::getStoreConfig('pointofsales/barcode_scanner/barcode_attribute');
        if ($barcodeAttributeCode == '')
                throw new Exception($this->__('Barcode attribute not set in Sytem > Configuration > Point of sales'));

        //search product
        $product = mage::getModel('catalog/product')
                                        ->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addAttributeToFilter($barcodeAttributeCode, $barcode)
                                        ->getFirstItem();

        return $product;
    }
}
