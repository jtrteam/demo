<?php
/**
 * Sitewards_B2BProfessional_Block_Reports_Product_Viewed
 * - rewrite the getAddToCartUrl function to check if the product is active
 *
 * @category    Sitewards
 * @package     Sitewards_B2BProfessional
 * @copyright   Copyright (c) 2013 Sitewards GmbH (http://www.sitewards.com/)
 */
class Sitewards_B2BProfessional_Block_Reports_Product_Viewed extends Mage_Reports_Block_Product_Viewed {
	/**
	 * Check if the product is valid
	 *  - return the replacement text for the add to cart url when required
	 *
	 * @param Mage_Catalog_Model_Product $oProduct
	 * @param array $aAdditional
	 * @return string
	 */
	public function getAddToCartUrl($oProduct, $aAdditional = array()) {
		/* @var $oB2BReplacementsHelper Sitewards_B2BProfessional_Helper_Replacements */
		$oB2BReplacementsHelper = Mage::helper('b2bprofessional/replacements');
		return $oB2BReplacementsHelper->replaceAddToCart($oProduct->getId(), parent::getAddToCartUrl($oProduct, $aAdditional));
	}
}