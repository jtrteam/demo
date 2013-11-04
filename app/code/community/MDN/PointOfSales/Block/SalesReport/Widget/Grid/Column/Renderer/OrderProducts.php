<?php

class MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderProducts
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
		$product_list = "";
    	$product_qte = "";
    	
    	//stock la valeur eco taxe de chaque élément de la commande
        foreach($row->getItemsCollection() as $item)
        {
			$qte = (int)$item->getqty_ordered();
			$product_list .= $qte."x ".$item->getname().'<br>';
			
        }
        
		return $product_list;
    }

}