<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.7.5
 * @license:     Y6BG0eVRwBRlH3J38c7aWdqCvw6zDo9AlBIeUllr4w
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2012 AITOC, Inc.
*/

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportReviewCustomerGrid
    extends Mage_Adminhtml_Block_Report_Review_Customer_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/review_customer_collection')->joinCustomers();

        if (!Mage::helper('aitpermissions')->isShowingAllCustomers())
        {
            $role = Mage::getSingleton('aitpermissions/role');
            
            if ($role->isPermissionsEnabled())
            {
                $collection->getSelect()->joinInner(
                    array('_table_customer' => Mage::getSingleton('core/resource')->getTableName('customer_entity')), 
                    ' _table_customer.entity_id = detail.customer_id ', 
                    array()
                    );

                $collection->addFieldToFilter('website_id', array('in' => $role->getAllowedWebsiteIds()));
            }
        }
        
        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}