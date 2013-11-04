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

class MDN_PointOfSales_Helper_ShelfLocation extends Mage_Core_Helper_Abstract {

    public function getShelfLocationForProduct($product){

        $shelf = $this->__('Undefined');

        $prefix = Mage::getConfig()->getTablePrefix();
		
        // check if ERP installed
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = 'SELECT * FROM '.$prefix.'core_resource WHERE code LIKE "%AdvancedStock_setup%"';
        $advancedStock = $read->fetchAll($sql);
        
        if(count($advancedStock) > 0){
            $version = $advancedStock[0]['version'];
            $tmp = explode('.',$version);
            if($tmp[0] > 1 || ($tmp[1] > 6 || ($tmp[1] == 6 && $tmp[2] > 7))){
                // retrieve location from erp helper
                $shelf = $product->getStockItem()->getshelf_location();
                $shelf = ($shelf == "") ? ' - ' : $shelf;
            }
            
        } else{
            // get from attribute
            $attribute_name = Mage::getStoreConfig('pointofsales/configuration/shelf_location_attribute_name');
            if($attribute_name != ""){
                $shelf = $product->getAttributeText($attribute_name);
            }
        }

        return $shelf;

    }

}

?>
