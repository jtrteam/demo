<?php

/*
* retourne le contenu d'une commande
*/
class MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderDetails
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$imgUrl = $this->getSkinUrl('images/lamp.gif');
    	$url = $this->getUrl('PointOfSales/SalesReport/OrderInfo', array('order_id' => $row->getId()));
    	$js = 'showOrderDetails(\''.$url.'\');';
    	$html = '<img src="'.$imgUrl.'" onclick="'.$js.'">';
    	return $html;
    }

}