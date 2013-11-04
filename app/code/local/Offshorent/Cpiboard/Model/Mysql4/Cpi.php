<?php

class Offshorent_Cpiboard_Model_Mysql4_Cpi extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
         $this->_init('cpiboard/cpi', 'cpi_id');
    }
}