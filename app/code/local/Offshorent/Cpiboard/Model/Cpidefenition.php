<?php

class Offshorent_Cpiboard_Model_Cpidefenition extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpiboard/cpidefenition');
    }
	
	public function isDataExists($data){
		 $collection = $this->getCollection();
		 $collection->addFieldToFilter('cpi_id',$data['cpi_id'])
					->addFieldToFilter('year',$data['year']);				
		 foreach ($collection as $defenition) {
				$id = $defenition->getId();
			}
			
			return $id;
	}
	
	
	public function getCpiLimit($period,$cpi_id){
		 $collection = $this->getCollection();		
		 $collection->addFieldToFilter('cpi_id',$cpi_id);
		 $collection->addFieldToFilter('year',date('Y'));
		 foreach ($collection as $defenition) {
			  if($period == '1m'){
				$field = 'get'.date('M');  
			    $qty = $defenition->$field();
		      }
			  else if($period == '1y'){
				  $qty = 0;
				for($i=1;$i<13;$i++){
					$field = 'get'.date('M', mktime(0,0,0,$i));  
					$qty = $qty +($defenition->$field());
				}
		      }
				$id = $defenition->getId();
		  }
			
			return $qty;
	}
}