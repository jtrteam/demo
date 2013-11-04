<?php

class MDN_PointOfSales_Model_Pdf_Order extends MDN_PointOfSales_Model_Pdf_Helper {

    private $_ECO_TAX_HEIGHT = 15;

    public function getPdf($orders = array()) {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $eco_tax_total = 0;

        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();
        else
            $this->firstPageIndex = count($this->pdf->pages);

        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        $order = $orders[0];

            //cree la nouvelle page
            $customer = mage::getmodel('customer/customer')->load($order->getCustomerId());
            $settings = array();
            $settings['title'] = Mage::Helper('PointOfSales')->__('Order # ') . $order->getincrement_id();
            $settings['store_id'] = $order->getStoreId();
            $page = $this->NewPage($settings);


            //cartouche
            $real_date = strtotime($order->getCreatedAt());
            $order_real_date = strtotime($order->getCreatedAt());
            $txt_date = Mage::Helper('PointOfSales')->__('Date : ') . date('d/m/Y', $real_date);
            $txt_order = "";
            $adresse_fournisseur = Mage::getStoreConfig('sales/identity/address');
            $adresse_client = $this->FormatAddress($order->getBillingAddress(), '', false, $customer->gettaxvat());
            $this->AddAddressesBlock($page, $adresse_fournisseur, $adresse_client, $txt_date, $txt_order);

            //ajoute le mode de livraison & le mode de paiement
            $modePaiement = '';
            try
            {
                if ($order->getPayment())
                    $modePaiement = $order->getPayment()->getMethodInstance()->gettitle();
            }
            catch(Exception $ex)
            {
                $modePaiement = $ex->getMessage();
            }
                
            $modeLivraison = $order->getShippingDescription();
            $this->drawPaymentShippingMode($page, $modePaiement, $modeLivraison);

            //affiche l'entete du tableau
            $this->drawTableHeader($page);

            $this->y -=10;
            $taxe_percent = "-";

            //Affiche les lignes produit
            //print_r($order->getAllItems());
            foreach ($order->getAllItems() as $item) {


                //recupere le taux de taxe
                if (($taxe_percent == '-') || ($item->gettax_percent() > $taxe_percent))
                    $taxe_percent = number_format ($item->gettax_percent(), 2, '.', '');

                //Si c'est un sous produit, on affiche pas
                if ($item->getParentItem()) {
                    continue;
                }

                //Pour les produits "standards"
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
                $page->drawText($this->TruncateTextToWidth($page, $item->getsku(), 60), 15, $this->y, 'UTF-8');
                $caption = $this->WrapTextToWidth($page, $item->getname(), 200);

                //si le produit a des enfants (c'est que c'est une config) et on d�crit son contenu
                $caption .= $this->getChildsForItem($item->getId(), $order->getAllItems());

                //rajoute les options du produits (si yen a)
                $options = $item->getProductOptions();
                if (isset($options['options'])) {
                    foreach ($options['options'] as $option) {
                        $caption .= "\n" . $option['value'];
                    }
                    $caption = str_replace(",", "\n", $caption);
                }

                $offset = $this->DrawMultilineText($page, $caption, 90, $this->y, 10, 0.2, 11);
                $this->drawTextInBlock($page, $order->formatPriceTxt(round($item->getbase_price() + $item->getbase_weee_tax_applied_amount(), 2)), 300, $this->y, 60, 20, 'r');
                $this->drawTextInBlock($page, (int) $item->getQty(), 370, $this->y, 40, 20, 'c');
                $this->drawTextInBlock($page, $taxe_percent, 410, $this->y, 40, 20, 'c');
                $this->drawTextInBlock($page, $order->formatPriceTxt($item->getRowTotal() + $item->getweee_tax_applied_row_amount()), 450, $this->y, 60, 20, 'r');
                $this->drawTextInBlock($page,
                        $order->formatPriceTxt(
                                $item->getRowTotal() +
                                $item->getTaxAmount() +
                                $item->getweee_tax_applied_row_amount() +
                                $item->getweee_tax_row_disposition()
                        ),
                        520,
                        $this->y,
                        60,
                        20,
                        'r');

                $this->y -= $offset;

                //on cumule l eco tax
                $eco_tax_ht = $item->getbase_weee_tax_applied_amount();
                if ($eco_tax_ht > 0) {
                    $eco_tax_total += $item->getweee_tax_applied_row_amount() + $item->getweee_tax_row_disposition();
                }

                $this->y -= $this->_ITEM_HEIGHT;

                //si on a plus la place de rajouter le footer, on change de page
                if ($this->y < ($this->_BLOC_FOOTER_HAUTEUR + 40)) {
                    $this->drawFooter($page);
                    $page = $this->NewPage($settings);
                    $this->drawTableHeader($page);
                }
            }

            //rajoute les frais d'exp�dition
            $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);
            $this->DrawMultilineText($page, Mage::Helper('PointOfSales')->__("Shipping Cost"), 90, $this->y, 10, 0.2, 11);
            $this->drawTextInBlock($page, $order->formatPriceTxt($order->getbase_shipping_amount()), 450, $this->y, 60, 20, 'r');
            if ($taxe_percent > 0)
                $this->drawTextInBlock($page, $order->formatPriceTxt($order->getbase_shipping_tax_amount() + $order->getbase_shipping_amount()), 520, $this->y, 60, 20, 'r');
            else
                $this->drawTextInBlock($page, $order->formatPriceTxt($order->getbase_shipping_amount()), 520, $this->y, 60, 20, 'r');

            //si on a plus la place de rajouter le footer, on change de page
            if ($this->y < (150)) {
                $this->drawFooter($page);
                $page = $this->NewPage($settings);
                $this->drawTableHeader($page);
            }

            //barre grise d�but totaux
            $this->y -= 10;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

            //barre verticale de s�paration des totaux
            $VerticalLineHeight = 90;

            if ($eco_tax_total > 0)
                $VerticalLineHeight += 20;

            $page->drawLine($this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2, $this->y - $VerticalLineHeight);

            //rajoute les libell�s & les totaux
            $this->y -= 20;


            //Zone commentaires
            $comments = $this->getComments($order);
            if (($comments != '') && ($comments != null)) {
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.3));
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
                $page->drawText(Mage::Helper('PointOfSales')->__('Comments'), 15, $this->y, 'UTF-8');
                $comments = $this->WrapTextToWidth($page, $comments, $this->_PAGE_WIDTH / 2);
                $this->DrawMultilineText($page, $comments, 15, $this->y - 15, 10, 0.2, 11);
            }

            //add discount
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
            if ($order->getdiscount_amount() > 0) {
                $Discount = $order->getdiscount_amount();
                $page->drawText(Mage::Helper('PointOfSales')->__('Discount '), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
                $this->drawTextInBlock($page, $order->formatPriceTxt(-$Discount), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 40, 'r');
            }
            $this->y -= 20;

            //totals
            $page->drawText(Mage::Helper('PointOfSales')->__('Total'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
            $this->drawTextInBlock($page, $order->formatPriceTxt(round($order->getbase_shipping_amount() + $order->getsubtotal() - $order->getdiscount_amount(), 2)), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 130, 40, 'r');
            $this->y -= 10;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), 10);
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
            $this->y -= 10;
            $TvaAmount = $order->getgrand_total() - round($order->getbase_shipping_amount() + $order->getsubtotal() - $order->getdiscount_amount(), 2);
            $page->drawText(Mage::Helper('PointOfSales')->__('Tax'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');

            if ($taxe_percent > 0)
                $this->drawTextInBlock($page, $order->formatPriceTxt($TvaAmount), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 130, 40, 'r');
            else
                $this->drawTextInBlock($page, $order->formatPriceTxt(0), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 130, 40, 'r');
            $this->y -= 20;
            $page->drawText(Mage::Helper('PointOfSales')->__('Total + Tax'), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
            $this->drawTextInBlock($page, $order->formatPriceTxt($order->getgrand_total()), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 130, 40, 'r');

            if ($eco_tax_total > 0) {
                $this->y -= 20;
                $page->drawText(Mage::Helper('PointOfSales')->__("WEEE"), $this->_PAGE_WIDTH / 2 + 10, $this->y, 'UTF-8');
                $this->drawTextInBlock($page, $order->formatPriceTxt($eco_tax_total), $this->_PAGE_WIDTH / 2, $this->y, $this->_PAGE_WIDTH / 2 - 30, 140, 'r');
            }

            //barre grise fin totaux
            $this->y -= 10;
            $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);


            //dessine le pied de page
            $this->drawFooter($page);

        //rajoute la pagination
        $this->AddPagination($this->pdf);

        $this->_afterGetPdf();

        return $this->pdf;
    }

    /**
     * Dessine l'entete du tableau avec la liste des produits
     *
     * @param unknown_type $page
     */
    public function drawTableHeader(&$page) {

        //entetes de colonnes
        $this->y -= 15;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        $page->drawText(Mage::Helper('PointOfSales')->__('SKU'), 15, $this->y, 'UTF-8');
        $page->drawText(Mage::Helper('PointOfSales')->__('Description'), 90, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, Mage::Helper('PointOfSales')->__('Price'), 300, $this->y, 60, 20, 'r');
        $this->drawTextInBlock($page, Mage::Helper('PointOfSales')->__('Qty'), 370, $this->y, 40, 20, 'c');
        $this->drawTextInBlock($page, Mage::Helper('PointOfSales')->__('Tax'), 410, $this->y, 40, 20, 'c');
        $this->drawTextInBlock($page, Mage::Helper('PointOfSales')->__('Total'), 450, $this->y, 60, 20, 'r');
        $this->drawTextInBlock($page, Mage::Helper('PointOfSales')->__('Total + Tax'), 520, $this->y, 60, 20, 'r');

        //barre grise fin entete colonnes
        $this->y -= 8;
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);

        $this->y -= 15;
    }

    /**
     * Dessine le mode de livraison & de paiement
     *
     */
    public function drawPaymentShippingMode(&$page, $paymentMode, $shippingMode) {
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);

        //barre grise debut
        $this->y -= 17;

        //on verifie si la longueur du texte n'est pas trop longue sinon on la coupe en plusieur ligne
        $txt_payment = $this->WrapTextToWidth($page, Mage::Helper('PointOfSales')->__('Payment : ') . $paymentMode, ($this->_PAGE_WIDTH / 2) - 20);
        $txt_Shipping = $this->WrapTextToWidth($page, Mage::Helper('PointOfSales')->__('Shipping : ') . $shippingMode, ($this->_PAGE_WIDTH / 2) - 20);

        //on recupere les hauteurs de text
        $height_payment = $this->DrawMultilineText($page, $txt_payment, 25, $this->y, 10, 0.2, 12);
        $height_shipping = $this->DrawMultilineText($page, $txt_Shipping, 312, $this->y, 10, 0.2, 12);

        $hauteur_txt = 0;
        //on recupere la plus haute
        if ($height_shipping > $height_payment)
            $hauteur_txt = $height_shipping;
        else
            $hauteur_txt = $height_payment;

        //on dessine la ligne de separation
        $x = $this->_PAGE_WIDTH / 2 - 50;
        $page->drawLine($x, $this->y + 17, $x, $this->y - $hauteur_txt - 12);

        $this->y -= $hauteur_txt;
        //barre grise fin

        $this->y -= 12;
        //$page->drawLine($this->_PAGE_WIDTH / 2, $this->y + 17, $this->_PAGE_WIDTH / 2, $this->y);
        $page->drawLine(10, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
    }

    /**
     * Retourne la liste format�e des enfants pour un parent
     *
     * @param unknown_type $itemId
     * @param unknown_type $AllItems
     * @return unknown
     */
    public function getChildsForItem($itemId, $AllItems) {
        $retour = '';
        foreach ($AllItems as $item) {
            if ($item->getparent_item_id() == $itemId)
                $retour .= "\n  " . ((int) $item->getqty_ordered()) . 'x ' . $item->getname();
        }
        return $retour;
    }

    /**
     *
     * @param <type> $order return comments for order
     */
    protected function getComments($order)
    {
        $comments = '';
        foreach($order->getStatusHistoryCollection() as $history)
        {
            if ($history->getcomment())
                $comments .= $history->getcomment()."\n";
        }
        return $comments;
    }
}
