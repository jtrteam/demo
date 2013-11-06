<?php

class Offshorent_AdminShare_Block_Widget_Grid_Column_Filter_Search extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text
{
    
    public function getCondition()
    {
        if ($this->getValue()) {
        	$value = $this->getValue();
        	$value = str_replace(' ', '%', $value);
        	$value = str_replace('-', '%', $value);
        	return array('like' => '%'.$value.'%');
        }
    }
}