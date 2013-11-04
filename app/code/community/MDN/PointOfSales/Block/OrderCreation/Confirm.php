<?php

class MDN_PointOfSales_Block_OrderCreation_Confirm extends Mage_Core_Block_Template
{
	private $_order = null;
	
	/**
	 * Return order
	 *
	 * @return unknown
	 */
	public function getOrder()
	{
		if ($this->_order == null)
		{
			$orderId = $this->getRequest()->getParam('order_id');
			$this->_order = mage::getModel('sales/order')->load($orderId);
			Mage::register('current_order', $this->_order);
		}
		return $this->_order;
	}
	
	/**
	 * 
	 *
	 */
	public function getDownloadInvoiceLink()
	{
		$invoices = $this->getOrder()->getInvoiceCollection();
		$retour = '';
		foreach ($invoices as $invoice)
		{
			$retour = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_invoice/print', array('invoice_id' => $invoice->getId()));
		}
		return  $retour;
	}
	
	/**
	 * 
	 *
	 */
	public function getDownloadShipmentLink()
	{
		$shipments = $this->getOrder()->getShipmentsCollection();
		$retour = '';
		foreach ($shipments as $shipment)
		{
			$retour = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order_shipment/print', array('invoice_id' => $shipment->getId()));
		}
		return  $retour;
	}

        /**
         * 
         */
        public function getDownloadOrderUrl()
        {
                return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/downloadOrder', array('order_id' => $this->getOrder()->getId()));

        }

        public function getDownloadReceiptUrl(){
            return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/downloadReceipt', array('order_id' => $this->getOrder()->getId()));
        }
	
	/**
	 * Enter description here...
	 *
	 */
	public function getPrintInvoiceLink()
	{
		return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/printInvoice', array('order_id' => $this->getOrder()->getId()));
	}
		
	/**
	 * Enter description here...
	 *
	 */
	public function getPrintShipmentLink()
	{
		return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/printShipment', array('order_id' => $this->getOrder()->getId()));
	}

        public function getPrintOrderLink()
        {
            return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/printOrder', array('order_id' => $this->getOrder()->getId()));
        }
	
	/**
	 * 
	 *
	 */
	public function getDisplayOrderLink()
	{
		return Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $this->getOrder()->getId()));
	}

        public function getNewOrderUrl()
        {
            return Mage::helper('adminhtml')->getUrl('PointOfSales/PointOfSales/StepOne');
        }
	
	/**
	 * 
	 *
	 * @return unknown
	 */
	public function getOrderId()
	{
		return $this->getOrder()->getincrement_id();
	}
	
	public function getOrderInfoData()
    {
        return array(
            'no_use_order_link' => true,
        );
    }

    /**
     * return currency to use
     *
     * @return unknown
     */
    public function getCurrency() {
        return mage::helper('PointOfSales/User')->getCurrency();
    }
	
	public function getOrderTotal()
	{
		return $this->getOrder()->getgrand_total();
	}
	
}