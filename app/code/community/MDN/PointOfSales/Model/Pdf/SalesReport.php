<?php

class MDN_PointOfSales_Model_Pdf_SalesReport extends MDN_PointOfSales_Model_Pdf_Helper {

    public function getPdf($dates = array()) {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $this->pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        //Add new page
        $settings = array();
        $settings['title'] = mage::helper('PointOfSales')->__('Sales Report');
        $storeId = mage::helper('PointOfSales/User')->getStoreId();
        $settings['store_id'] = $storeId;
        $page = $this->NewPage($settings);

        $totals = array();
        $attributes = array();

        //add table header
        $this->drawTableHeader($page);

        //print order collection
        $collection = $dates[0];
        foreach ($collection as $item) {

            //print order information
            $paymentMethod = '';
            try {
                $paymentMethod = $item->getPayment()->getMethodInstance()->gettitle();
            } catch (Exception $ex) {
                $paymentMethod = $ex->getMessage();
            }
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
            $page->drawText($item->getincrement_id(), 15, $this->y, 'UTF-8');

            $billing_name = $this->TruncateTextToWidth($page, $item->getbilling_name(), 98);
            $page->drawText($billing_name, 100, $this->y, 'UTF-8');
            $page->drawText($paymentMethod, 285, $this->y, 'UTF-8');
            $page->drawText($this->FormatPrice($item->getGrandTotal()), 400, $this->y, 'UTF-8');
            $page->drawText($this->formatDate($item->getcreated_at()), 500, $this->y, 'UTF-8');

            //update totals
            if (!isset($totals[$paymentMethod]))
                $totals[$paymentMethod] = 0;
            $totals[$paymentMethod] += $item->getGrandTotal();

            //add products
            $this->y -= 20;
            foreach ($item->getAllItems() as $item) {
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 8);
                $txt = '- ' . ((int) $item->getqty_ordered()) . 'x ' . $item->getName();
                $page->drawText($txt, 120, $this->y, 'UTF-8');
                $this->y -= 15;

                //new page
                if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                    $this->drawFooter($page, $storeId);
                    $page = $this->NewPage($settings);
                    $this->drawTableHeader($page);
                }
                // get product id
                $productId = $item->getproduct_id();
                // load product to get this attributes
                $product = Mage::getModel('catalog/product')->load($productId);
                // get attribute name
                $attributeSetName = Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId())->getAttributeSetName();
                // stock attribute
                if (!array_key_exists($product->getAttributeSetId(), $attributes)) {
                    $attributes[$product->getAttributeSetId()] = array( "total"=>0, "qty"=>0);
                }
                $attributes[$product->getAttributeSetId()]["name"] = $attributeSetName;
                $attributes[$product->getAttributeSetId()]["total"] += $item->getrow_total();
                $attributes[$product->getAttributeSetId()]["qty"] += (int) $item->getqty_ordered();
            }


            //separation line
            $page->setLineWidth(0.5);
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.1));
            $page->drawLine(10, $this->y - 5, $this->_BLOC_ENTETE_LARGEUR, $this->y - 5);

            $this->y -= $this->_ITEM_HEIGHT;

            //new page
            if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                $this->drawFooter($page, $storeId);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
            }
        }

        //draw totals
        $this->drawTotals($totals, $page, $attributes);
        $this->drawFooter($page, $storeId);
        $this->AddPagination($this->pdf);
        $this->_afterGetPdf();

        return $this->pdf;
    }

    /**
     * Draw table headers
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page) {
        $this->y -= 15;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);

        $page->drawText(mage::helper('PointOfSales')->__('Order #'), 15, $this->y, 'UTF-8');
        $page->drawText(mage::helper('PointOfSales')->__('Customer'), 100, $this->y, 'UTF-8');
        $page->drawText(mage::helper('PointOfSales')->__('Payment Method'), 285, $this->y, 'UTF-8');
        $page->drawText(mage::helper('PointOfSales')->__('Total'), 400, $this->y, 'UTF-8');
        $page->drawText(mage::helper('PointOfSales')->__('Date'), 500, $this->y, 'UTF-8');

        $this->y -= 8;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 15;
    }

    /**
     * Draw totals per payment method
     *
     */
    public function drawTotals($totals, &$page, $attributes) {

        //echo'<pre>'; print_r($attributes); echo'</pre>'; die('*');
        // add line
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 25;

        // display total by attribute
        foreach ($attributes as $attribute) {
            $attributeLine = $attribute["qty"] . ' x ' . $attribute["name"] . ''; // 14 x Default € 99 = 41931
            $page->drawText(mage::helper('PointOfSales')->__('%s', $attributeLine), 285, $this->y, 'UTF-8');
            $page->drawText(mage::helper('PointOfSales')->__($this->FormatPrice($attribute["total"])), 500, $this->y, 'UTF-8');
            $this->y -= 25;
        }

        // add line
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 25;

        // display total by payment method
        foreach ($totals as $key => $value) {
            $page->drawText(mage::helper('PointOfSales')->__('Total %s', $key), 285, $this->y, 'UTF-8');
            $page->drawText($this->FormatPrice($value), 500, $this->y, 'UTF-8');
            $this->y -= 25;
        }

        //display grand total
        $grandTotal = 0;
        foreach ($totals as $key => $value) {
            $grandTotal += $value;
        }
        $page->drawText(mage::helper('PointOfSales')->__('Total'), 285, $this->y, 'UTF-8');
        $page->drawText($this->FormatPrice($grandTotal), 500, $this->y, 'UTF-8');

        $this->y -= 15;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 15;
    }

    /**
     * format price
     *
     * @param unknown_type $price
     * @return unknown
     */
    public function FormatPrice($price) {
        $currency = mage::helper('PointOfSales/User')->getCurrency();
        return $currency->format($price, array(), false);
    }

    /**
     * Format date
     *
     * return only date like YYYY-MM-DD
     *
     * @param string $date
     */
    public function formatDate($date) {

        $tmp = explode(" ", $date);
        return $tmp[0];
    }

}
