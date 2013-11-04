<?php

class MDN_PointOfSales_Helper_Currency extends Mage_Core_Helper_Abstract {

    /**
     * Convert price
     *
     */
    public function convert($priceInclTax) {

        $base_currency = Mage::getModel('directory/currency')->load(Mage::getStoreConfig('currency/options/base'));
        $to_currency_code = Mage::getSingleton('admin/session')->getUser()->getcurrency();
        $to_currency = Mage::getModel('directory/currency')->load($to_currency_code);

        $value = $priceInclTax;
        try
        {
            $value = $base_currency->convert($priceInclTax, $to_currency);
        }
        catch(Exception $ex)
        {
            Mage::logException($ex);
        }

        return $value;
    }

}

