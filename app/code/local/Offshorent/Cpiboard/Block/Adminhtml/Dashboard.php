<?php

class Offshorent_Cpiboard_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Dashboard
{
    public function __construct()
    {
        parent::__construct();
		if (!Mage::helper('cpiboard')->canCpiboard()) {
        	$this->setTemplate('dashboard/index.phtml');
		}else{
			$head = Mage::app()->getLayout()->getBlock('head');
            $head->addItem('skin_css', 'cpiboard/css/main.css');
			if (Mage::helper('cpiboard')->canAddJquery()) {
				$head->addItem('skin_js', 'cpiboard/js/jquery.js');
				$head->addItem('skin_js', 'cpiboard/js/jquery.noconflict.js');
			}
			$head->addItem('skin_js', 'cpiboard/js/rotate.js');
			$this->setTemplate('cpiboard/index.phtml');
			
		}
 		
    }
	
	/*----------------- Return Order Intake Persentage---------------*/
	
	public function getOrderIntake()  
    {
        
        $period = $this->getRequest()->getParam('period', '1m');

        $collection = Mage::getResourceModel('reports/order_collection')
                ->addCreateAtPeriodFilter($period)
                ->calculateTotals(1);
		if ($this->getRequest()->getParam('store') > 0 ) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        }		
        $collection->load();
        $totals = $collection->getFirstItem();
        $totalsQty = $totals->getQuantity()*1; 
		
		$cpiLimit  = Mage::getModel('cpiboard/cpidefinition')->getCpiLimit($period,4,$this->getRequest()->getParam('store'));
		if($cpiLimit > 0){
			if($cpiLimit > $totalsQty)
			   $percentage = round(($totalsQty*100)/$cpiLimit);
			else
			   $percentage = 100;
		}else{
			 $percentage = 0;
		}
		return $percentage;
		
    }
	
	/*----------------- Return Total Quote Persentage---------------*/
	
	public function getQuote()
    {
        $period = $this->getRequest()->getParam('period', '1m');

        $collection = Mage::getResourceModel('reports/quote_collection')
				  ->addFieldToFilter('created_at',$this->getPeriodFilter($period));	
		if ($this->getRequest()->getParam('store') > 0 ) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        }	
        $collection->load();
        $totalsQty = $collection->count()*1; 
        $cpiLimit  = Mage::getModel('cpiboard/cpidefinition')->getCpiLimit($period,1,$this->getRequest()->getParam('store'));
		if($cpiLimit > 0){
			if($cpiLimit > $totalsQty)
			   $percentage = round(($totalsQty*100)/$cpiLimit);
			else
			   $percentage = 100;
		}else{
			 $percentage = 0;
		}
		return $percentage;
    }
	
	/*----------------- Return Total Complaints---------------*/
	
	public function getComplaints()
    {
       $period = $this->getRequest()->getParam('period', '1m');	   

         $collection = Mage::getModel('CrmTicket/Ticket')->getCollection()
		          ->addFieldToFilter('ct_created_at',$this->getPeriodFilter($period));
		if ($this->getRequest()->getParam('store') > 0 ) {
            $collection->addFieldToFilter('ct_store_id', $this->getRequest()->getParam('store'));
        }
        $totalsQty = $collection->count()*1; 
     	$cpiLimit  = Mage::getModel('cpiboard/cpidefinition')->getCpiLimit($period,5,$this->getRequest()->getParam('store'));
		if($cpiLimit > 0){
			if($cpiLimit > $totalsQty)
			   $percentage = round(($totalsQty*100)/$cpiLimit);
			else
			   $percentage = 100;
		}else{
			 $percentage = 0;
		}
		return $percentage;
    }
   
   /*----------------- Return Total visitors---------------*/
	
	public function getVisitors()
    {
       $period = $this->getRequest()->getParam('period', '1m');

        $collection = Mage::getModel('log/visitor')->getCollection()
				->addFieldToFilter('first_visit_at',$this->getPeriodFilter($period));
		if ($this->getRequest()->getParam('store') > 0 ) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        }
        $collection->load();
        $totalsQty = $collection->count()*1; 
     	$cpiLimit  = Mage::getModel('cpiboard/cpidefinition')->getCpiLimit($period,6,$this->getRequest()->getParam('store'));
		if($cpiLimit > 0){
			if($cpiLimit > $totalsQty)
			   $percentage = round(($totalsQty*100)/$cpiLimit);
			else
			   $percentage = 100;
		}else{
			 $percentage = 0;
		}
		return $percentage;
    }
	
	public function getSettingUrl()
    {
        return $this->getUrl('cpiboard/adminhtml_cpiboard');
     
    }
	
	public function getPeriodFilter($period)
    {
        list($from, $to) = $this->helper('cpiboard')->getDateRange($period, 0, 0, true);
        $periodFilter = array(
            'from'  => $from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'    => $to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
        );

        return $periodFilter;
    }
	
}
