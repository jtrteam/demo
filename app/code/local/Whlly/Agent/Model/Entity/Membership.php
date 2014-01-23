<?php
class Whlly_Agent_Model_Entity_Membership extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            $this->_options[] = array(
                    'value' => 0,
                    'label' => 'Member'
            );
            $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Agent'
            );
             
        }
 
        return $this->_options;
    }
}