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

class Aitoc_Aitpermissions_Block_Rewrite_AdminReportShopcartAbandonedGrid
    extends Mage_Adminhtml_Block_Report_Shopcart_Abandoned_Grid
{
    protected function _prepareCollection()
    {
        $role = Mage::getSingleton('aitpermissions/role');
        
        $collection = Mage::getResourceModel('reports/quote_collection');
        
        $filter = $this->getParam($this->getVarNameFilter(), array());
        
        if ($filter) {
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
        }
        
        if (!empty($data)) {
            $collection->prepareForAbandonedReport($this->_storeIds, $data);
        } else {
            $collection->prepareForAbandonedReport($this->_storeIds);
        }
          
        if ($role->isPermissionsEnabled())
        {
            if (!Mage::helper('aitpermissions')->isShowingAllCustomers())
            {
                $collection->getSelect()->joinLeft(array(
                        'customer' => Mage::getSingleton('core/resource')->getTableName('customer_entity')),
                        'customer.entity_id = main_table.customer_id',
                        array()
                    );
                
                $collection->addStoreFilter($role->getAllowedStoreviewIds());                
            }              
        }
        
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}