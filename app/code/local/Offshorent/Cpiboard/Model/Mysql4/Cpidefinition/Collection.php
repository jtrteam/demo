<?php

class Offshorent_Cpiboard_Model_Mysql4_Cpidefinition_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpiboard/cpidefinition');
    }
}