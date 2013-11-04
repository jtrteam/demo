<?php
/* 
 * Magento
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Nicolas MUGNIER
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_PointOfSales_Helper_CheckClientComputer extends Mage_Core_Helper_Abstract{

    public function isInstalled(){

        $reader = Mage::getSingleton('core/resource')->getConnection('core_read');

        $prefix = $prefix = Mage::getConfig()->getTablePrefix();
        
        $sql = 'SELECT COUNT(*) AS count FROM '.$prefix.'core_resource WHERE code LIKE "%ClientComputer_setup%"';

        $res = $reader->fetchAll($sql);

        return ($res[0]['count'] == 0 ) ? false : true;

    }


}
?>
