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

class MDN_PointOfSales_Model_System_Config_Unit extends Mage_Eav_Model_Entity_Attribute_Source_Abstract{

    public function getAllOptions(){

        if(!$this->_options){
            
            $this->options[] = array(
                    'value'=>'cm',
                    'label'=>'cm'
            );
            
            $this->_options[] = array(
                'value'=>'inch',
                'label'=>'inch'
            );
        }

        return $this->_options;

    }

    public function toOptionArray(){
        return $this->getAllOptions();
    }

}
?>
