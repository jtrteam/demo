<?php

class Offshorent_Cpiboard_Model_Cpi extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpiboard/cpi');
    }
}