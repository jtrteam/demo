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
class MDN_PointOfSales_Helper_Serialization extends Mage_Core_Helper_Abstract
{
	
	public function serializeObject($object)
	{
		if ($object == null)
			$object = $this->initObject();
		return urlencode(serialize($object));
	}
	
	public function unserializeObject($raw)
	{
		return unserialize(urldecode($raw));		
	}
		
	
	public function initObject()
	{
		$retour = array();
		$retour['customer_id'] = null;
		$retour['payment_method'] = null;
		$retour['shipping_method'] = null;
		$retour['products'] = array();
		$retour['customer_firstname'] = null;
		$retour['customer_lastname'] = null;
		$retour['is_paid'] = 0;
		$retour['is_shipped'] = 0;
		return $retour;
	}
}
