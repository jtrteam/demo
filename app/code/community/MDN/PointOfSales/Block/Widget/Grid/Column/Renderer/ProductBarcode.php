<?php

class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductBarcode
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$html = mage::helper('PointOfSales/Barcode')->getBarcodeForProduct($row);
    	return $html;
    }

}