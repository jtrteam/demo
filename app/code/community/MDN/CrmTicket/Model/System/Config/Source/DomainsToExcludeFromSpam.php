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
 * @copyright  Copyright (c) 2013 BoostMyshop (http://www.boostmyshop.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @package MDN_CrmTicket
 * @version 1.2
 */
class MDN_CrmTicket_Model_System_Config_Source_DomainsToExcludeFromSpam extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options)
        {
            $this->getAllOptions();
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
        	$this->_options = array();

			$collection = mage::getModel('CrmTicket/EmailSpam')
                    ->getCollection()
                    ->addFieldToFilter('cesr_exclude', 1)
                    ->setOrder('cesr_domain', 'asc');

			foreach($collection as $item)
			{
	        	$this->_options[] = array('value' => $item->getcesr_domain() ,'label' => $item->getcesr_domain());
			}
        }
        return $this->_options;
    }

}