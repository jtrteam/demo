<?php

class MDN_PointOfSales_Block_OrderCreation_StepOne extends Mage_Core_Block_Template {

    /**
     * return data serialized
     *
     * @return unknown
     */
    public function getRaw() {
        return mage::helper('PointOfSales/Serialization')->serializeObject(null);
    }

    /**
     * return payment methods
     *
     */
    public function getPaymentMethods() {
        //$storeId = mage::helper('PointOfSales/User')->getStoreId();
        //$carriers = Mage::getModel('Payment/config')->getActiveMethods($storeId);
        //return $carriers;

        $methods = array();
        $methods[] = mage::getModel('PointOfSales/Payment_BankTerminal');
        $methods[] = mage::getModel('PointOfSales/Payment_Check');
        $methods[] = mage::getModel('PointOfSales/Payment_Money');
        return $methods;
    }

    /**
     * return shipping methods
     *
     */
    public function getShippingMethods() {
        $carriers = Mage::getStoreConfig('carriers', 0);
        $retour = array();
        foreach ($carriers as $item) {
            $instance = mage::getModel($item['model']);
            $instance->setmodel($item['model']);
            $retour[] = $instance;
            //$retour .= '<option value="'.$instance->_code.'">'.$instance->getConfigData('title').'</option>';
        }
        return $retour;
    }

    /**
     * Return coutrny
     *
     * @param unknown_type $name
     * @return unknown
     */
    public function getCountryCombo($name, $value) {
        $country = mage::helper('PointOfSales/User')->getDefaultCountry();
        $value = $country->getcountry_id();

        $retour = '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '">' . $country->getName();
        return $retour;
    }

    /**
     * Return combobox with regions for default country
     * @param <type> $name
     * @param <type> $value
     */
    public function getRegionCombo($name) {
        $country = mage::helper('PointOfSales/User')->getDefaultCountry();
        $regions = $country->getRegions();

        $regionCode = Mage::getSingleton('admin/session')->getUser()->getregion();

        $html = '<select name="'.$name.'" id="'.$name.'">';

        foreach($regions as $region)
        {
            $selected = '';
            if ($region->getcode() == $regionCode)
                    $selected = ' selected ';
            $html .= '<option value="'.$region->getId().'" '.$selected.'>'.$region->getName().'</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * return currency to use
     *
     * @return unknown
     */
    public function getCurrency() {
        return mage::helper('PointOfSales/User')->getCurrency();
    }

    public function getCreateShipmentCheckedValue() {
        if (mage::getStoreConfig('pointofsales/configuration/default_shipment_tick'))
            return ' checked ';
        else
            return '';
    }

    public function getCreateInvoiceCheckedValue() {
        if (mage::getStoreConfig('pointofsales/configuration/default_invoice_tick'))
            return ' checked ';
        else
            return '';
    }

}