<?php
 
class MDN_CrmTicket_Model_Mysql4_EmailRouterRules_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('CrmTicket/EmailRouterRules');
    }

}