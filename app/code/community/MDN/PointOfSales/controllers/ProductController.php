<?php

class MDN_PointOfSales_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function InfoAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $html = mage::helper('PointOfSales/ProductInfo')->getInfoForPopup($productId);
        $this->getResponse()->setBody($html);
    }
    
    public function GridAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();
    }
}