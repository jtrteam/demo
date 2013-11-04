<?php


class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductStock
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return mage::helper('PointOfSales/Stock')->getProductStockInfo($row);
    }

}