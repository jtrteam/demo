<?php

class MDN_PointOfSales_OtherSalesController extends Mage_Adminhtml_Controller_Action
{
	public function GridAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function AjaxAction()
	{
        $this->loadLayout();
        $Block = $this->getLayout()->createBlock('PointOfSales/OtherSales_Grid');
        $this->getResponse()->setBody($Block->toHtml());
	}
}