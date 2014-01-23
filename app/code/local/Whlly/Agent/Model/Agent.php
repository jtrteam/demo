<?php

class Whlly_Agent_Model_Agent extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('agent/agent');
    }
}