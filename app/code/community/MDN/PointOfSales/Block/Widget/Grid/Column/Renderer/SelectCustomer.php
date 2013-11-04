<?php

/*
* retourne le contenu d'une commande
*/
class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_SelectCustomer
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
    	$retour = '<a href="javascript:selectCustomer('.$row->getId().',\''.addslashes($row->getName()).'\',\''.$this->getUrl('PointOfSales/PointOfSales/customerBillingAdress').'\');">Select</a>';
        return $retour;
    }
}