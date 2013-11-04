<?php

class MDN_PointOfSales_Block_Customer_Widget_Grid_Column_Renderer_CustomerDetails
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$imgUrl = $this->getSkinUrl('images/lamp.gif');
    	$url = $this->getUrl('PointOfSales/Customer/CustomerInfo', array('customer_id' => $row->getId()));
    	$js = 'showOrderDetails(\''.$url.'\');';
    	$html = '<img src="'.$imgUrl.'" onclick="'.$js.'">';
    	return $html;
    }

}