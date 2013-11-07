<?php

class Offshorent_AdminShare_Block_Share_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
        $this->setId('ProductGrid');
        $this->setDefaultSort('entity_id');
        $this->_parentTemplate = $this->getTemplate();
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = $this->getRequest()->getParam('store');
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
    	
    	$storeId   = $this->getRequest()->getParam('store');
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();

        $store = mage::getModel('core/store')->load($storeId); //$this->_getStore();
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');
                
       
        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
        }        
        
		/*-------------------SPNCDN ( Permission Filter ------------------*/
		$role = Mage::getSingleton('aitpermissions/role');
		if($role->isPermissionsEnabled()){
				$getCurrentUserRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
				$bCanEditOwn = Mage::getModel('aitpermissions/advancedrole')->canEditOwnProductsOnly($getCurrentUserRole);
			   
				if ($bCanEditOwn === true && Mage::getStoreConfig('admin/general/showallproducts') != true)
				{
				   
					
					if ($role->isScopeStore() &&
						!in_array($controllerName, array('sales_order_edit', 'sales_order_create')))
					{
						$collection->getSelect()->joinLeft(array(
							'product_cat' => $collection->getTable('catalog/category_product')),
							'product_cat.product_id = e.entity_id',
							array()
						);
			
						if (Mage::helper('aitpermissions')->isShowingProductsWithoutCategories() == 1)
						{
							$collection->getSelect()->where(
								' product_cat.category_id in (' . join(',', $role->getAllowedCategoryIds()) . ')
							or product_cat.category_id IS NULL '
							);
						}
						else
						{
							$collection->getSelect()->where(
								' product_cat.category_id in (' . join(',', $role->getAllowedCategoryIds()) . ')'
							);
						}
			
						$collection->getSelect()->distinct(true);
					}
					
					if ($role->isScopeWebsite())
					{
						$websiteIds = $role->getAllowedWebsiteIds();
						$scopeStoreId = Mage::app()->getFrontController()->getRequest()->getParam('store');
			
						if ($scopeStoreId)
						{
							$scopeWebsiteId = Mage::getModel('core/store')->load($scopeStoreId)->getWebsiteId();
			
							if (in_array($scopeWebsiteId, $websiteIds))
							{
								$websiteIds = array($scopeWebsiteId);
							}
						}
						
						$collection->addWebsiteFilter($websiteIds);
					}
			
					if ($bCanEditOwn === true)
					{
						$idSubAdmin = Mage::getSingleton('admin/session')->getUser()->getId();
						$collection->addAttributeToFilter('created_by', $idSubAdmin);
					}
				
				
				 }
		 }
		
		/*-------------------SPNCDN ( Permission Filter ------------------*/
		$visibility = array(
		   Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		   Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);
		$collection->addAttributeToFilter('visibility', $visibility);
		$collection->addAttributeToSort('updated_at', 'DESC');
        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
                
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {    	
       
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'filter'    => 'adminshare/widget_grid_column_filter_search'
        ));

       
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
		
		$this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '70px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_type')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
		
		$this->addColumn('action', array(
            'header' => Mage::helper('sales')->__('Share'),
            'renderer'  => 'Offshorent_AdminShare_Block_Widget_Grid_Column_Renderer_ProductShare',
            'index' => 'content',
            'filter' => false,
            'align' => 'center'
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }
}