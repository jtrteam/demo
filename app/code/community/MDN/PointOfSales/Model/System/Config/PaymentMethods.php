<?php

/**
 * Magento Fianet Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gr
 * @package    Gr_Fianet
 * @author     Nicolas Fabre <nicolas.fabre@groupereflect.net>
 * @copyright  Copyright (c) 2008 Nicolas Fabre
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_PointOfSales_Model_System_Config_PaymentMethods extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        if (!$this->_options) {
            $config = Mage::getStoreConfig('payment');
            foreach ($config as $code => $methodConfig) {
                if (isset($methodConfig['title'])) {
                    $options[] = array(
                        'value' => $code,
                        'label' => $methodConfig['title'],
                    );
                }
            }
            $this->_options = $options;
        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

    /**
     * Return POS methods (money, check, bank terminal)
     * @return <type>
     */
    public function getPosMethods()
    {
        $methods = array();
        $config = Mage::getStoreConfig('payment');
        foreach ($config as $code => $methodConfig) {
            if (($code == 'Check') || ($code == 'Money') || ($code == 'BankTerminal'))
            {
                if (isset($methodConfig['title'])) {
                    $methods[] = array(
                        'value' => $code,
                        'label' => $methodConfig['title'],
                    );
                }
            }
        }
        return $methods;
    }

}