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

class Aitoc_Aitpermissions_Model_Approve extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/approve', 'id');
    }

/*
 * @refactor
 * load single record by product id
 * rename to isProductApproved
 */
    public function isApproved($productId)
    {
        $collection = array();
        $collection = $this->getCollection()->loadByProductId($productId);
        foreach ($collection as $item)
        {
            return $item->getIsApproved();
        }

        return true;
    }

/*
 * @refactor
 * second parameter does not correspond to method name/purpose
 * split into separate methods approveProduct / disapproveProduct
 * remove magic number
 */
    public function approve($productId, $status = 1)
    {
        if ($status == Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING)
        {
            $status = 0;
        }
        
        $collection = $this->getCollection()->loadByProductId($productId);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $item)
            {
                $item->setIsApproved($status)->save();
            }
        }
        else
        {
            $this->setProductId($productId)->setIsApproved($status)->save();
        }

        return true;
    }
}