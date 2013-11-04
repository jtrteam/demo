<?php

class MDN_PointOfSales_CustomerController extends Mage_Adminhtml_Controller_Action
{
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$this->_redirect('adminhtml/customer/edit', array('id' => $id));
	}
	
	public function GridAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function CustomerInfoAction()
	{
 		$customerId = $this->getRequest()->getParam('customer_id');
		$customer = mage::getModel('customer/customer')->load($customerId);
		mage::register('current_customer', $customer);
			
        //load layout with handle
        $handles = array('default', 'pointofsales_customer_info');
        $this->loadLayout($handles);

        //change layout
        $rootBlock = $this->getLayout()->getBlock('root');
        $rootBlock->setTemplate('page/popup.phtml');

        //return
        $output = $this->getLayout()->getOutput();
        $this->getResponse()->setBody($output);
	}
	
	public function AjaxAction()
	{
        $this->loadLayout();
        $Block = $this->getLayout()->createBlock('PointOfSales/Customer_Grid');
        $this->getResponse()->setBody($Block->toHtml());
	}
}