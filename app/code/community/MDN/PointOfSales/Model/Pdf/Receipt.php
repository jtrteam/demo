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

class MDN_PointOfSales_Model_Pdf_Receipt extends MDN_PointOfSales_Model_Pdf_Helper {

    protected $_BLOC_ENTETE_HAUTEUR = 50;
    protected $_BLOC_ENTETE_LARGEUR;
    protected $_BLOC_FOOTER_HAUTEUR = 40;
    protected $_BLOC_FOOTER_LARGEUR;
    protected $_PAGE_HEIGHT;
    protected $_PAGE_WIDTH;
    protected $_LOGO_HAUTEUR = 50;
    protected $_LOGO_LARGEUR = 50;
    protected $_ITEM_HEIGHT = 25;
    protected $width;
    protected $height;
    protected $margin_left;
    private $_forced_height = 0;
    private $orders;
    private $_fontSize = 10;
    private $_lineHeight = 12;

    /**
     * Init
     *
     * @param float $width
     * @param string $unit
     * @param Order $orders
     */
    public function initSizes($width, $unit, $orders) {

        // add margin left for some printers
        $margin_left = (Mage::getStoreConfig('pointofsales/receipt/margin_left') != '') ? Mage::getStoreConfig('pointofsales/receipt/margin_left') : 0;

        // convert unit according to configuration in system.xml
        switch ($unit) {
            case 'cm':
                $this->width = ((($width) / 2.54) * 595) / 8.26;
                $this->margin_left = (($margin_left / 2.54) * 595) / 8.26;
                break;
            case 'inch':
                $this->width = ($width) * 595 / 8.26;
                $this->margin_left = $margin_left * 595 / 8.26;
                break;
        }

        $this->_lineHeight = $this->_fontSize * ( 1 + 20 / 100);
        $this->_fontSize = 8;

        // set const
        $this->_BLOC_ENTETE_LARGEUR = $this->width - 10;
        $this->_BLOC_PAGE_WIDTH = $this->width;
        $this->_BLOC_FOOTER_LARGEUR = $this->width - 10;

        $cpt = count($orders[0]->getAllItems());

        // adjust pdf height
        $this->height = ($this->_forced_height == 0) ? $this->_ITEM_HEIGHT * $cpt : $this->_forced_height;
    }

    /**
     * generate PDF
     *
     * @param array $orders
     * @return Zend_Pdf
     */
    public function getPdf($orders = array()) {

        // save order in class attribute, used for check pdf height
        $this->orders = &$orders;

        $this->_beforeGetPdf();

        // init
        $this->initSizes(Mage::getStoreConfig('pointofsales/receipt/width'), Mage::getStoreConfig('pointofsales/receipt/unit'), $orders);

        if ($this->pdf == null)
            $this->pdf = new Zend_Pdf();

        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), $this->_fontSize);

        // create new page
        $settings['title'] = Mage::getStoreConfig('pointofsales/receipt/header');
        ;
        $settings['store_id'] = null;
        $page = $this->NewPage($settings); // add header
        // get first order
        $order = $orders[0];

        // labels
        $page->drawText(Mage::helper('PointOfSales')->__('QTY'), 5 + $this->margin_left, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('PointOfSales')->__('PRODUCT'), 40 + $this->margin_left, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('PointOfSales')->__('PRICE'), ($this->width * 70 / 100), $this->y, 'UTF-8');

        $this->y -= 15;

        //Affiche les lignes produit
        foreach ($order->getAllItems() as $item) {

            //Si c'est un sous produit, on affiche pas
            if ($item->getParentItem()) {
                continue;
            }

            //Pour les produits "standards"
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $this->_fontSize);

            // [1]
            // add qty
            $qty_txt = (int) $item->getqty_ordered();
            $page->drawText($qty_txt, 5 + $this->margin_left, $this->y, 'UTF-8');
            // [1]
            // [2]
            // build line : ajout de retour a la ligne afin que le texte rentre dans la largeur passee en parametre
            $caption_width = 40 * $this->width / 100;
            $caption = $this->WrapTextToWidth($page, $item->getname(), $caption_width);

            // add caption (product name and options)
            // return height of caption block
            $offset = $this->DrawMultilineText($page, $caption, 25 + $this->margin_left, $this->y, $this->_fontSize, 0.2, $this->_lineHeight, false);
            // [2]
            // [3]
            // add price
            $price_width = 20 * $this->width / 100;
            $price_pos = $this->width * 70 / 100;
            $this->drawTextInBlock($page, $order->formatPriceTxt(round($item->getrow_total_incl_tax(), 2)), $price_pos, $this->y, $price_width, 20, 'r');
            // [3]

            $this->y -= $offset;
        }

        $this->y -= 5;

        //totals
        $txt_pos = $price_pos - $price_width - 50;
        $page->drawText(Mage::Helper('PointOfSales')->__('Sub Total'), $txt_pos, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $order->formatPriceTxt(round($order->getsubtotal() - $order->getdiscount_amount(), 2)), $price_pos, $this->y, $price_width, 20, 'r');

        $this->y -= 10;

        $shipping_amount = $order->getbase_shipping_amount();
        if ($shipping_amount > 0) {

            $page->drawText(Mage::Helper('PointOfSales')->__('Shipping'), $txt_pos, $this->y, 'UTF-8');
            $this->drawTextInBlock($page, $order->formatPriceTxt(round($order->getbase_shipping_amount(), 2)), $price_pos, $this->y, $price_width, 20, 'r');

            $this->y -= 10;
        }

        // check taxes
        $page->drawText(Mage::Helper('PointOfSales')->__('Tax'), $txt_pos, $this->y, 'UTF-8');

        $this->drawTextInBlock($page, $order->formatPriceTxt(round($order->getbase_tax_amount() + $order->getbase_shipping_tax(), 2)), $price_pos, $this->y, $price_width, $this->_lineHeight, 'r');

        $this->y -= 10;

        // grand total
        $page->drawText(Mage::Helper('PointOfSales')->__('Total'), $txt_pos, $this->y, 'UTF-8');
        $this->drawTextInBlock($page, $order->formatPriceTxt($order->getgrand_total()), $price_pos, $this->y, $price_width, $this->_lineHeight, 'r');

        $this->y -= 10;

        //add date & order id
        $page->drawLine(5, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 15;
        $infos = Mage::helper('PointOfSales')->__('Order') . ' #' . $order->getincrement_id();
        $infos .= "\n" . $order->getcreated_at();
        $offset = $this->DrawMultilineText($page, $infos, 5 + $this->margin_left, $this->y, $this->_fontSize, 0.2, $this->_lineHeight, false);
        $this->y -= $offset;

        // add footer
        $this->drawFooter($page);

        $this->_afterGetPdf();

        // check pdf height, if to long, build another one according to content
        $this->checkHeight();

        return $this->pdf;
    }

    /**
     * Check page height
     */
    public function checkHeight() {

        if ($this->y < 0) {

            $this->_forced_height = $this->height - $this->y + 20;
            $this->pdf = null;
            $this->getPdf($this->orders);
        }
    }

    /**
     * Cree une nouvelle page (et dessine son entete)
     *
     */
    public function NewPage(array $settings = array()) {

        $page = $this->pdf->newPage($this->width . ':' . $this->height . ':');
        $this->pdf->pages[] = $page;

        //on place Y tout en haut
        $this->y = $this->height;

        //dessine l'entete
        $this->drawHeader($page, $settings['title'], $settings['store_id']);

        //retourne la page
        return $page;
    }

    /**
     * Dessine l'entete de la page
     */
    public function drawHeader(&$page, $header, $StoreId = null) {

        // [1]
        // affichage nom de la societe en gras
        $this->y -= 20;
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), $this->_fontSize);
        $this->drawTextInBlock($page, Mage::getStoreConfig('pointofsales/receipt/store_name'), 5, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->_lineHeight, 'c', 'UTF-8');
        // [1]
        $this->y -= 20;
        // [2]
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $this->_fontSize);
        $offset = $this->DrawMultilineText($page, $header, 5 + $this->margin_left, $this->y, $this->_fontSize, 0.2, $this->_lineHeight, false);
        // affichage texte header
        // [2]
        $this->y -= $offset;
        // [3]
        // affichage ligne continue
        $page->drawLine(5, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        // [3]
        $this->y -= 15;
    }

    /**
     * Dessine le pied de page
     *
     * @param unknown_type $page
     */
    public function drawFooter(&$page, $StoreId = null) {

        $page->drawLine(5, $this->y, $this->_BLOC_ENTETE_LARGEUR, $this->y);
        $this->y -= 15;

        //rajoute le texte
        $offset = $this->DrawMultilineText($page, Mage::getStoreConfig('pointofsales/receipt/footer', $StoreId), 5 + $this->margin_left, $this->y, $this->_fontSize, 0, $this->_lineHeight, false);

        $this->y -= $offset + 5;
    }

    /**
     * get product childs
     *
     * @param int $itemId
     * @param array $AllItems
     * @return string
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
     * Draw multiline text
     *
     * @param <type> $page
     * @param <type> $Text
     * @param <type> $x
     * @param <type> $y
     * @param <type> $Size
     * @param <type> $GrayScale
     * @param <type> $LineHeight
     * @param <type> $allowNewPage
     * @return string
     */
    protected function DrawMultilineText(&$page, $Text, $x, $y, $Size, $GrayScale, $LineHeight, $allowNewPage = false) {
        $retour = 0;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale($GrayScale));
        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $Size);

        foreach (explode("\n", $Text) as $value) {
            if ($value !== '') {

                $page->drawText(trim(strip_tags($value)), $x, $y, 'UTF-8');
                $y -=$LineHeight;
                $retour += $LineHeight;
            }
        }
        return $retour;
    }

}

?>
