<?php

/*
* retourne le contenu d'une commande
*/
class MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderPaymentMethod
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$html = '?';
        try
        {
            if ($row->getPayment())
                    $html = $row->getPayment()->getMethodInstance()->gettitle();
        }
        catch(Exception $ex)
        {
            $html = $ex->getMessage();
        }
    	return $html;
    }

}