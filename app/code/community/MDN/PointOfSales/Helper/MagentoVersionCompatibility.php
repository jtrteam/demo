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
class MDN_PointOfSales_Helper_MagentoVersionCompatibility extends Mage_Core_Helper_Abstract {

    private $_isFlatOrder = null;

    /**
     * Return true if magento version uses flat model for orders
     * Return false if magento uses EAV model for orders
     * Used to keep only one branch for both eav model and flat model
     *
     */
    public function isFlatOrder() {
        if ($this->_isFlatOrder == null) {
            $tableName = mage::getResourceModel('sales/order')->getTable('sales/order');
            if ($tableName == $this->getTablePrefix() . 'sales_order')
                $this->_isFlatOrder = false;
            else
                $this->_isFlatOrder = true;
        }
        return $this->_isFlatOrder;
    }

    /**
     * Same function, reverse name
     *
     * @return unknown
     */
    public function ordersUseEavModel() {
        return!$this->isFlatOrder();
    }

    /**
     * Return table prefix
     *
     * @return unknown
     */
    public function getTablePrefix() {
        return (string) Mage::getConfig()->getTablePrefix();
    }

    /**
     * return version
     *
     * @return unknown
     */
    public function getVersion() {
        $version = mage::getVersion();
        $t = explode('.', $version);
        return $t[0] . '.' . $t[1] . '.' . $t[2];
    }

    public function getSalesOrderAddressEntityId() {

        return ($this->ordersUseEavModel()) ? Mage::getResourceModel("sales/order_address")->getTypeId() : 0;
    }

}