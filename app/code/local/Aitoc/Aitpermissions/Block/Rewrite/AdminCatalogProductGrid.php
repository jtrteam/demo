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

class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductGrid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $role = Mage::getSingleton('aitpermissions/role');

        if (!$role->isPermissionsEnabled() || $role->isAllowedToDelete())
        {
            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('catalog')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('catalog')->__('Are you sure?')
            ));
        }

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('catalog')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        $this->getMassactionBlock()->addItem('attributes', array(
            'label' => Mage::helper('catalog')->__('Update attributes'),
            'url' => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current' => true))
        ));

        if(!$role->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $owners = Mage::getSingleton('aitpermissions/source_admins')->getOptionArray();

            array_unshift($owners, array('label' => '', 'value' => ''));
            $this->getMassactionBlock()->addItem('created_by', array(
                'label' => Mage::helper('catalog')->__('Set owner'),
                'url' => $this->getUrl('aitpermissions/adminhtml_catalogProduct/massOwner', array('_current' => true)),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'created_by',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('catalog')->__('Owner name'),
                        'values' => $owners
                    )
                )
            ));

        }
        return $this;
    }
    
    protected function _toHtml()
    {
        if(Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled()) 
		{
            $allowedWebisteIds = Mage::getSingleton('aitpermissions/role')->getAllowedWebsiteIds();
            if (count($allowedWebisteIds) <= 1)
            {
                unset($this->_columns['websites']);
            }
        }
        return parent::_toHtml();
    }

    protected function _prepareCollection()
    {
        $this->_allowUpdateCollection = true;
        parent::_prepareCollection();
        $this->_allowUpdateCollection = false;
        return $this;
    }

    public function setCollection($collection)
    {
        if($this->_allowUpdateCollection && !Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $collection->joinAttribute('created_by', 'catalog_product/created_by', 'entity_id', null, 'left');
        }
        return parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        if(!Mage::getSingleton('aitpermissions/role')->isPermissionsEnabled() && Mage::helper('aitpermissions')->isShowProductOwner()) {
            $this->addColumnAfter('created_by',
                array(
                    'header'=> Mage::helper('aitpermissions')->__('Owner'),
                    'width' => '70px',
                    'index' => 'created_by',
                    'type'  => 'options',
                    'options' => Mage::getSingleton('aitpermissions/source_admins')->getOptionArray(),
            ), 'status');
            $this->sortColumnsByOrder();
        }
        return $this;
    }
}