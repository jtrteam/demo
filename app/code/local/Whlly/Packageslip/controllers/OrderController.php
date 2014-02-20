<?php
include_once("Mage/Adminhtml/controllers/Sales/OrderController.php");
class Whlly_Packageslip_OrderController extends Mage_Adminhtml_Sales_OrderController
{
   
    public function pdfshipmentsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
				//------Custom Code Start ------------//				
				else {					
					$flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('packageslip/packageslip')->getPdf($orderId);
                    } else {
                        $pages = Mage::getModel('packageslip/packageslip')->getPdf($orderId);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
					
				}
					
				
				
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

  
}
