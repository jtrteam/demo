<?php
/* 
 * Magento
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Nicolas MUGNIER
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MDN_PointOfSales_ReceiptController extends Mage_Adminhtml_Controller_Action {

    public function printAction(){
        try{
            //create pdf and download
            $order = Mage::getModel('sales/order')->load('1');
            $pdf = Mage::getModel('PointOfSales/Pdf_Receipt')->getPdf(array($order));
            //$name = mage::helper('PointOfSales')->__('order_') . $order->getincrement_id() . 'pdf';
            //$name = str_replace('/', '-', $name);

            // save in exchange directory
            $name = 'receipt.pdf';
            mage::helper('ClientComputer')->printDocument($pdf->render(), $name, 'Point Of Sales : print receipt');
            $this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');

        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage().' : '.$e->getTraceAsString());
            $this->_redirectReferer();
        }
    }

}
?>
