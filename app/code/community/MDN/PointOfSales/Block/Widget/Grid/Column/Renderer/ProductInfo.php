<?php

class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductInfo
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$imgUrl = $this->getSkinUrl('images/lamp.gif');
    	$url = $this->getUrl('PointOfSales/Product/Info', array('product_id' => $row->getId()));
    	$js = 'showProductDetails(\''.$url.'\');';
    	$html = '<img height="22" width="22" src="'.$imgUrl.'" onclick="'.$js.'">';
    	return $html;
    }

}