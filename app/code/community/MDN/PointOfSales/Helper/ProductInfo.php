<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_PointOfSales_Helper_ProductInfo extends Mage_Core_Helper_Abstract
{
    public function getInfoForPopup($productId)
    {
        $product = mage::getModel('catalog/product')->load($productId);

        //create block & return html
        $layout = Mage::getSingleton('core/layout');
        $html = $layout->createBlock('PointOfSales/OrderCreation_ProductInfo')
                ->setTemplate('PointOfSales/OrderCreation/ProductInfo.phtml')
                ->setProduct($product)
                ->toHtml();
        
        return $html;
    }
}