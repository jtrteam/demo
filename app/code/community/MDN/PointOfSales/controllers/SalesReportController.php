<?php

class MDN_PointOfSales_SalesReportController extends Mage_Adminhtml_Controller_Action {

    /**
     * Display sales report grid
     *
     */
    public function GridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Print order report
     *
     */
    public function PrintAction() {
        try {
            //get order collection
            $block = $this->getLayout()->createBlock('PointOfSales/SalesReport_Grid');
            $block->toHtml();
            $collection = $block->getCollection();

            $pdf = Mage::getModel('PointOfSales/Pdf_SalesReport')->getPdf(array($collection));
            $name = mage::helper('PointOfSales')->__('sales_report_').'pdf';
            $name = str_replace('/', '-', $name);
            $this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : %s', $ex->getMessage()));
            $this->_redirect('PointOfSales/SalesReport/Grid');
        }
    }

    public function AjaxAction() {
        $this->loadLayout();
        $Block = $this->getLayout()->createBlock('PointOfSales/SalesReport_Grid');
        $this->getResponse()->setBody($Block->toHtml());
    }

    public function OrderInfoAction() {
        $orderId = $this->getRequest()->getParam('order_id');

        //load layout with handle
        $handles = array('default', 'pointofsales_pointofsales_confirm');
        $this->loadLayout($handles);

        //change layout
        $rootBlock = $this->getLayout()->getBlock('root');
        $rootBlock->setTemplate('page/popup.phtml');

        //return
        $output = $this->getLayout()->getOutput();
        $this->getResponse()->setBody($output);
    }

}
