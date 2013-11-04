<?php

class MDN_PointOfSales_PointOfSalesController extends Mage_Adminhtml_Controller_Action {

    private $_OrderCreationData = null;

    /**
     * First step to create an order
     *
     */
    public function StepOneAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * Return Billing adresse for existing customer
     *
     */

    public function customerBillingAdressAction() {

        $customer_id = $this->getRequest()->getPost('customer_id');

        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $customer_adresse = $customer->getDefaultBillingAddress();

        //retourne
        $response = array(
            'street' => ($customer_adresse->getstreet()),
            'city' => $this->__($customer_adresse->getcity()),
            'zip' => ($customer_adresse->getpostcode()),
            'country_id' => ($customer_adresse->getcountry_id()),
            'phone' => ($customer_adresse->gettelephone()),
            'mobile' => ($customer_adresse->getmobile()),
            'fax' => ($customer_adresse->getfax())
        );


        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * Return customer grid to search existing customer
     *
     */
    public function customerGridAction() {
        $this->loadLayout();
        $Block = $this->getLayout()->createBlock('PointOfSales/OrderCreation_CustomerGrid');
        $this->getResponse()->setBody($Block->toHtml());
    }

    /**
     * Return products grid
     *
     */
    public function productsGridAction() {
        $this->loadLayout();
        $Block = $this->getLayout()->createBlock('PointOfSales/OrderCreation_ProductsGrid');
        $this->getResponse()->setBody($Block->toHtml());
    }

    /**
     * Confirm order creation
     *
     */
    public function ConfirmAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Print invoice
     *
     */
    public function printInvoiceAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = mage::getModel('sales/order')->load($orderId);
        if ($order->getId()) {
            $this->printInvoice($order);
        }
        else
            die('Unable to load order');
    }

    /**
     * Print shipment
     *
     */
    public function printShipmentAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = mage::getModel('sales/order')->load($orderId);
        if ($order->getId()) {
            $this->printShipment($order);
        }
        else
            die('Unable to load order');
    }

    public function printOrderAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = mage::getModel('sales/order')->load($orderId);
        if ($order->getId()) {
            $this->printOrder($order);
        }
        else
            die('Unable to load order');
    }

    /*     * ******************************************************************************************************************************
     * ********************************************************************************************************************************
     * ** Shipping & Payment method
     * ********************************************************************************************************************************
     * ******************************************************************************************************************************* */

    /**
     * Return payment form depending of the payment method
     *
     */
    public function PaymentFormAction() {
        $html = '';

        try {
            $paymentMethodCode = $this->getRequest()->getParam('payment_method');
            $paymentMethod = '';
            $formBlockType = $paymentMethod->getFormBlockType();
            $formBlock = $this->getLayout()->createBlock($formBlockType);

            //return form in json
            $html = $formBlock->toHtml();
        } catch (Exception $ex) {
            $html = $this->__('An error occured : %s', $ex->getMessage());
        }

        //return html
        $this->getResponse()->setBody($html);
    }

    /**
     * Return shipping methods
     *
     */
    public function ShippingMethodAction() {
        //init vars
        $error = false;
        $message = '';
        $shippingRates = '';

        try {
            //load and init datas & create order
            $this->loadData();
            $this->initData();

            $shippingRates = mage::helper('PointOfSales')->getShippingRates($this->_OrderCreationData);
        } catch (Exception $e) {
            $error = true;
            $message = $this->__('An error occured : %s', $e->getMessage() . ' - ' . $e->getTraceAsString());
        }

        //return ajax result
        $response = array();
        $response['error'] = $error;
        $response['message'] = $message;
        $response['shippingRates'] = $shippingRates;
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /* *******************************************************************************************************************************
     * ********************************************************************************************************************************
     * ** ORDER CREATION
     * ********************************************************************************************************************************
     * ******************************************************************************************************************************* */

    /**
     * Create order and return result with ajax
     *
     */
    public function CreateOrderAction() {
        //init vars
        $error = false;
        $message = '';

        try {
            //load and init datas & create order
            $this->loadData();
            $this->initData();

            $this->checkStocks();

            //If new customer selected, create customer
            if ($this->_OrderCreationData['customer_mode'] == 'new') {
                $customer = mage::helper('PointOfSales/Customer')->createCustomer($this->_OrderCreationData);
                $address = mage::helper('PointOfSales/Customer')->createAddress($this->_OrderCreationData, $customer->getId());

                $this->_OrderCreationData['customer_id'] = $customer->getId();
            }

            //newsletter subscription
            if ($this->getRequest()->getPost('newsletter')) {
                Mage::helper('PointOfSales/Customer')->newsletterSubscription($this->_OrderCreationData['customer_id']);
            }


            $order = mage::helper('PointOfSales')->createOrder($this->_OrderCreationData);

            //create order / shipment
            if ($this->_OrderCreationData['is_paid']) {
                $invoice = mage::helper('PointOfSales')->createInvoice($order, $this->_OrderCreationData['invoice_comments']);
            }
            mage::helper('PointOfSales')->createShipment($order, $this->_OrderCreationData);

            //print invoice (is required)
            if (mage::getStoreConfig('pointofsales/configuration/print_invoice')) {
                $this->printInvoice($order);
            }

            //print shipment (is required)
            if (mage::getStoreConfig('pointofsales/configuration/print_shipment')) {
                $this->printShipment($order);
            }

            if (Mage::getStoreConfig('pointofsales/configuration/print_receipt')) {
                $this->printReceipt($order);
            }

            // notify guest customer if congiguration allow it
            if( Mage::getStoreConfig('pointofsales/notification/enable_new_order_email') ){
                $order->sendNewOrderEmail();
            }
                  
            //return url
            $url = $this->getUrl('PointOfSales/PointOfSales/Confirm', array('order_id' => $order->getId()));
            $message = $url;
        } catch (Exception $e) {
            $error = true;
            $exceptionText = $e->getMessage();

            //improve error msg
            $quoteSession = Mage::getSingleton('adminhtml/session_quote');
            $messages = $quoteSession->getMessages(true);
            if ($messages->count() > 0)
                $exceptionText = $messages->getLastAddedMessage()->toString();

            if ($exceptionText == '')
                $exceptionText = $this->__('Undefined exception');

            $message = $this->__($exceptionText);
        }

        //return ajax result
        $response = array();
        $response['error'] = $error;
        $response['message'] = $message;
        $response = Zend_Json::encode($response);
        $this->getResponse()->setBody($response);
    }

    /**
     * Check that all products can be purchased (check stock levels)
     */
    protected function checkStocks() {
        foreach ($this->_OrderCreationData['products'] as $product) {
            $productId = $product['product_id'];
            $qty = $product['qty'];

            $product = Mage::getModel('catalog/product')->load($productId);
            $stockItem = $product->getStockItem();
            if ($stockItem) {
                if ($stockItem->getManageStock()) {
                    if (!$stockItem->checkQty($qty))
                        throw new Exception($this->__('The requested quantity for %s is not available.', $product->getName()));
                }
            }
        }
    }

    /**
     * load datas
     *
     */
    private function loadData() {
        $dataRaw = $this->getRequest()->getPost('rawdata');
        $this->_OrderCreationData = mage::helper('PointOfSales/Serialization')->unserializeObject($dataRaw);
    }

    /**
     * init with post data
     *
     */
    private function initData() {

        //add products
        if ($this->getRequest()->getPost('product_ids') != '') {
            $this->_OrderCreationData['products'] = array();
            $products = explode(';', $this->getRequest()->getPost('product_ids'));

            foreach ($products as $item) {
                $t = explode('-', $item);
                if (count($t) == 5) {
                    $id = $t[0];
                    $product = mage::getModel('catalog/product')->load($id);
                    $qty = $t[1];
                    $priceExclTax = $t[2];
                    $priceInclTax = $t[3];
                    $isShipped = $t[4];
                    $newProduct = array('product_id' => $id,
                        'qty' => $qty,
                        'priceExclTax' => $priceExclTax,
                        'priceInclTax' => $priceInclTax,
                        'shipped' => $isShipped);
                    $this->_OrderCreationData['products'][] = $newProduct;
                }
            }
        }

        //payment & shipping methods
        $this->_OrderCreationData['payment_method'] = $this->getRequest()->getPost('paymentmethod');
        $this->_OrderCreationData['shipping_method'] = $this->getRequest()->getPost('shippingmethod');

        if ($this->getRequest()->getPost('is_paid') != '')
            $this->_OrderCreationData['is_paid'] = $this->getRequest()->getPost('is_paid');

        if ($this->getRequest()->getPost('is_shipped') != '')
            $this->_OrderCreationData['is_shipped'] = $this->getRequest()->getPost('is_shipped');

        $this->_OrderCreationData['store_id'] = Mage::getSingleton('admin/session')->getUser()->getstore_id();

        //customer
        $this->_OrderCreationData['customer_mode'] = $this->getRequest()->getPost('customer_mode');

        $this->_OrderCreationData['comments'] = $this->getRequest()->getPost('comments');
        $this->_OrderCreationData['invoice_comments'] = $this->getRequest()->getPost('invoice_comments');

        //define information depending of customer mode
        $emptyString = mage::getStoreConfig('pointofsales/configuration/empty_string');
        switch ($this->_OrderCreationData['customer_mode']) {
            case 'guest':
                
                $this->_OrderCreationData['customer_firstname'] = $emptyString;
                $this->_OrderCreationData['customer_lastname'] = $emptyString;
                $this->_OrderCreationData['customer_email'] = Mage::getStoreConfig('pointofsales/notification/guest_account_email');

                //address
                $this->_OrderCreationData['customer_company'] = $emptyString;
                $this->_OrderCreationData['address'] = $emptyString;

                $defaultCity = mage::helper('PointOfSales/User')->getDefaultCity();
                if (!$defaultCity)
                    $this->_OrderCreationData['city'] = $emptyString;
                else
                    $this->_OrderCreationData['city'] = $defaultCity;

                $defaultZip = mage::helper('PointOfSales/User')->getDefaultZip();
                if (!$defaultZip)
                    $this->_OrderCreationData['zip'] = $emptyString;
                else
                    $this->_OrderCreationData['zip'] = $defaultZip;

                $this->_OrderCreationData['country'] = $this->getRequest()->getPost('country');
                $this->_OrderCreationData['mobile'] = $emptyString;
                $this->_OrderCreationData['phone'] = $emptyString;
                $this->_OrderCreationData['fax'] = $emptyString;
                $this->_OrderCreationData['customer_phone'] = $emptyString;

                break;
            default: //new, existing

                $this->_OrderCreationData['customer_id'] = $this->getRequest()->getPost('customer_id');
                $this->_OrderCreationData['customer_firstname'] = $this->getRequest()->getPost('customer_firstname');
                $this->_OrderCreationData['customer_lastname'] = $this->getRequest()->getPost('customer_lastname');
                $this->_OrderCreationData['customer_email'] = $this->getRequest()->getPost('customer_email');

                //address
                $this->_OrderCreationData['customer_company'] = $this->getRequest()->getPost('customer_company');
                $this->_OrderCreationData['address'] = $this->getRequest()->getPost('address');
                $this->_OrderCreationData['city'] = $this->getRequest()->getPost('city');
                $this->_OrderCreationData['zip'] = $this->getRequest()->getPost('zip');
                $this->_OrderCreationData['country'] = $this->getRequest()->getPost('country');
                $this->_OrderCreationData['mobile'] = $this->getRequest()->getPost('mobile');
                $this->_OrderCreationData['phone'] = $this->getRequest()->getPost('phone');
                $this->_OrderCreationData['fax'] = $this->getRequest()->getPost('fax');
                $this->_OrderCreationData['customer_phone'] = $this->getRequest()->getPost('customer_phone');
                $this->_OrderCreationData['region'] = $this->getRequest()->getPost('region');

                //fill required address information with empty string if not set
                $fields = array('address', 'city', 'zip', 'phone');
                foreach ($fields as $field) {
                    if ($this->_OrderCreationData[$field] == '')
                        $this->_OrderCreationData[$field] = $emptyString;
                }

                break;
        }
    }

    /**
     * Print invoce using magento client computer
     *
     * @param unknown_type $order
     */
    private function printInvoice($order) {
        //collect invoice
        $invoice = null;
        $order = mage::getModel('sales/order')->load($order->getId());
        foreach ($order->getInvoiceCollection() as $item) {
            $invoice = $item;
        }

        //print
        if ($invoice != null) {
            $invoicePdfModel = Mage::getModel('sales/order_pdf_invoice');
            $pdf = $invoicePdfModel->getPdf(array($invoice));
            if ($pdf != null && Mage::helper('PointOfSales/CheckClientComputer')->isInstalled()) {
                $fileName = 'invoice_' . $invoice->getincrement_id() . '.pdf';
                mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, 'Point of sales  - print invoice #' . $invoice->getincrement_id());
            }
        }
    }

    private function printReceipt($order) {

        $order = mage::getModel('sales/order')->load($order->getId());

        $pdf = Mage::getModel('PointOfSales/Pdf_Receipt')->getPdf(array($order));
        if ($pdf != null && Mage::helper('PointOfSales/CheckClientComputer')->isInstalled()) {
            $name = 'Receipt_' . $order->getincrement_id() . '.pdf';
            mage::helper('ClientComputer')->printDocument($pdf->render(), $name, 'Point Of Sales : print receipt');
        }
    }

    /**
     * Print shipment using magento client computer
     *
     * @param unknown_type $order
     */
    private function printShipment($order) {
        //collect shipment
        $shipments = $order->getShipmentsCollection();
        $shipment = null;
        foreach ($shipments as $item) {
            $shipment = $item;
        }

        //print
        if ($shipment != null) {
            $ShipmentPdfModel = Mage::getModel('sales/order_pdf_shipment');
            $pdf = $ShipmentPdfModel->getPdf(array($shipment));
            if ($pdf != null && Mage::helper('PointOfSales/CheckClientComputer')->isInstalled()) {
                $fileName = 'shipment_' . $shipment->getincrement_id() . '.pdf';
                mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, 'Point of sales  - print shipment #' . $shipment->getincrement_id());
            }
        }
    }

    /**
     * Print order using magento client computer
     *
     * @param unknown_type $order
     */
    private function printOrder($order) {
        //print
        if ($order != null && Mage::helper('PointOfSales/CheckClientComputer')->isInstalled()) {
            $pdf = Mage::getModel('PointOfSales/Pdf_Order')->getPdf(array($order));
            $fileName = mage::helper('PointOfSales')->__('order_') . $order->getincrement_id() . '.pdf';
            $fileName = str_replace('/', '-', $fileName);
            mage::helper('ClientComputer')->printDocument($pdf->render(), $fileName, 'Point of sales  - print order #' . $order->getincrement_id());
        }
    }

    /**
     * Print shipment using magento client computer
     *
     * @param unknown_type $order
     */
    public function downloadOrderAction() {
        //load order
        $orderId = $this->getRequest()->getParam('order_id');
        $order = mage::getModel('sales/order')->load($orderId);

        //create pdf and download
        $pdf = Mage::getModel('PointOfSales/Pdf_Order')->getPdf(array($order));
        $name = mage::helper('PointOfSales')->__('order_') . $order->getincrement_id() . 'pdf';
        $name = str_replace('/', '-', $name);
        $this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
    }

    public function downloadReceiptAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $pdf = Mage::getModel('PointOfSales/Pdf_Receipt')->getPdf(array($order));
        $name = 'Receipt_' . $order->getincrement_id() . '.pdf';
        $this->_prepareDownloadResponse($name, $pdf->render(), 'application/pdf');
    }

    /**
     * Add product from barcode
     *
     */
    public function AddProductFromBarcodeAction() {
        //init vars
        $barcode = $this->getRequest()->getParam('barcode');
        $barcode = str_replace('slash', '/', $barcode);
        $barcode = urldecode($barcode);

        $result = array();
        $result['error'] = 0;
        $result['message'] = '';
        $result['product_information'] = null;

        try {
            $product = mage::helper('PointOfSales/Barcode')->getProductFromBarcode($barcode);
            if (($product == null) || (!$product->getId()))
                throw new Exception($this->__('Cant find product with barcode = %s', $barcode));

            //define product information
            $store = mage::helper('PointOfSales/User')->getStore();
            $price = $product->getPrice();
            if ($product->getspecial_price() != '') {
                if (Mage::app()->getLocale()->isStoreDateInInterval($store, $product->getspecial_from_date(), $product->getspecial_to_date()))
                    $price = $product->getspecial_price();
            }

            //define tax rate
            $helper = Mage::getSingleton('tax/calculation');
            $defaultAddress = mage::helper('PointOfSales/User')->getFakeAddress();
            $request = $helper->getRateRequest($defaultAddress, $defaultAddress, false, $store);
            $request->setProductClassId($product->getTaxClassId());
            $taxRate = $helper->getRate($request);

            //define prices
            if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 1) {
                $priceInclTax = $price;
                $priceExclTax = $priceInclTax / (1 + ($taxRate / 100));
            } else {
                $priceExclTax = $price;
                $priceInclTax = $price * (1 + ($taxRate / 100));
            }

            //format prices
            $priceExclTax = number_format($priceExclTax, 2, '.', '');
            $taxRate = number_format($taxRate, 4, '.', '');
            $priceInclTax = number_format($priceInclTax, 2, '.', '');
            $productName = $this->cleanTxt($product->getname());

			// convert price
			$priceInclTax = Mage::helper('PointOfSales/Currency')->convert($priceInclTax);	
			
            //return product information
            $result['product_information'] = array();
            $result['product_information']['product_name'] = $productName;
            $result['product_information']['product_id'] = $product->getId();
            $result['product_information']['skin_url'] = Mage::getDesign()->getSkinUrl('images/OrderWizardCreation/');
            $result['product_information']['price_excl_tax'] = $priceExclTax;
            $result['product_information']['price_incl_tax'] = $priceInclTax;
            $result['product_information']['tax_rate'] = $taxRate;
            $result['product_information']['currency_symbol'] = Mage::app()->getLocale()->currency(Mage::getStoreConfig('PointOfSales/configuration/default_currency'))->getSymbol();

            $result['message'] = $this->__('%s added', $productName);
        } catch (Exception $ex) {
            $result['error'] = 1;
            $result['message'] = $ex->getMessage();
        }

        //return in json
        $response = Zend_Json::encode($result);
        $this->getResponse()->setBody($response);
    }

    public function cleanTxt($txt) {
        return addslashes(str_replace('"', ' ', $txt));
    }

    /**
     * Return change calculator form
     */
    public function ChangeCalculatorAction() {
        $block = $this->getLayout()->createBlock('core/template');
        $block->setTemplate('PointOfSales/ChangeCalculator.phtml');
        $this->getResponse()->setBody($block->toHtml());
    }

}