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

class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct extends Mage_Catalog_Model_Product
{
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled()
            && Mage::getStoreConfig('admin/su/enable')
            && !$this->getCreatedAt())
        {
            $this->setStatus(Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING);
            Mage::getModel('aitpermissions/notification')->send($this);
        }
        
        if ($this->getId() && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getId(), $this->getStatus());
        }

        $request = Mage::app()->getRequest();

        if (($request->getPost('simple_product') &&
            $request->getQuery('isAjax') &&
            $role->isScopeStore()) ||
            Mage::helper('aitpermissions')->isQuickCreate())
        {
            $this->_setParentCategoryIds($request->getParam('product'));
        }
        
        return $this;
    }
    
    private function _setParentCategoryIds($parentId)
    {
        $configurableProduct = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->load($parentId);

        if ($configurableProduct->isConfigurable())
        {
            if (!$this->getData('category_ids'))
            {
                $categoryIds = (array)$configurableProduct->getCategoryIds();
                if ($categoryIds)
                {
                    $this->setCategoryIds($categoryIds);
                }
            }
        }
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        
        if ($this->getData('entity_id') && Mage::getStoreConfig('admin/su/enable') && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getData('entity_id'), $this->getStatus());
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));

            if (($role->canEditOwnProductsOnly() && !$role->isOwnProduct($product)) ||
                !$role->isAllowedToEditProduct($product))
            {
                Mage::throwException(
                    Mage::helper('aitpermissions')->__(
                        'Sorry, you have no permissions to delete this product. For more details please contact site administrator.'
                    )
                );
            }
        }

        return $this;
    }
}