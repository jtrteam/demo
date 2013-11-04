<?php

class MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Filter_OrderProducts extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text
{
    
    public function getCondition()
    {
    	$searchString = $this->getValue();
    	if ($searchString == '')
    		return;

    	$orderIds = array();
    	$model = mage::getResourceModel('sales/order_item_collection');
    	$sql = $model->getSelect()
    				->where("(name like '%".$searchString."%')");
    	$collection = $model->getConnection()->fetchAll($sql);
		foreach ($collection as $item)
		{
			$orderIds[] = $item['order_id'];
		}
    	
    		
    	return array('in' => $orderIds);
    }
    
}