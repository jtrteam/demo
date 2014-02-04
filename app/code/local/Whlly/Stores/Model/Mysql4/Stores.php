<?php

class Whlly_Stores_Model_Mysql4_Stores extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('stores/stores', 'stores_id');
    }
}