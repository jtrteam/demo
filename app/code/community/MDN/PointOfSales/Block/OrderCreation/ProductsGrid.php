<?php

class MDN_PointOfSales_Block_OrderCreation_ProductsGrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('PointOfSalesProductGrid');
        //$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('point_of_sales_product_grid_filter');
		$this->setRowClickCallback(false);

    }

    protected function _getStore()
    {
        $storeId = Mage::getSingleton('admin/session')->getUser()->getstore_id();
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
    	
    	$storeId = Mage::getSingleton('admin/session')->getUser()->getstore_id();
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();

        $store = mage::getModel('core/store')->load($storeId); //$this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
			->addAttributeToSelect('image')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('ordered_qty')
            ->addAttributeToSelect('tax_class_id')            
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToFilter('type_id', 'simple')
            ->addAttributeToFilter('status', 1)
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1 and is_in_stock=1',
                'inner');
                
        //if barcode attribute set, add it
        $barcodeAttributeCode = mage::getStoreConfig('pointofsales/barcode_scanner/barcode_attribute');
        if ($barcodeAttributeCode)
        {
        	$collection->addAttributeToSelect($barcodeAttributeCode);
        }

        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $storeId);
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
            $collection->joinAttribute('special_price', 'catalog_product/special_price', 'entity_id', null, 'left', $storeId);
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('special_price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
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
        $this->addColumn('info',
            array(
                'header'=> '',
                'index' => 'info',
                'renderer' => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductInfo',
                'align' => 'center',
                'filter' => false,
                'sortable' => false
        ));		

        
        /*$this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));*/
        
        
		/*-----------Product Image--SPNCDZ-----------*/
		$this->addColumn('image',
			array(
				'header'=> Mage::helper('PointOfSales')->__('Product Image'),
				'index'=> 'image',
				'sortable'=> false,
				'filter'=> false,
				'renderer' => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductImage',
		));
       /*-----------Product Image---SPNCDZ----------*/
	   
        //if barcode feature enabled, add column
        $barcodeEnabled = mage::getStoreConfig('pointofsales/barcode_scanner/enable');		
        if ($barcodeEnabled)
        {
            $this->addColumn('barcode',
                array(
                    'header'=> Mage::helper('PointOfSales')->__('Barcode'),
                    'index' => 'entity_id',
                    'renderer' => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductBarcode',
                    'align' => 'center',
                    'filter' => false,
                    'sortable' => false
            ));
        }

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'filter'    => 'PointOfSales/Widget_Grid_Column_Filter_ProductSearch'
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();
		/** quickNdirty **/
		/*        $this->addColumn('set_name',
					array(
						'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
						'width' => '100px',
						'index' => 'attribute_set_id',
						'type'  => 'options',
						'options' => $sets,
				));
		*/
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price (incl tax)'),
                'index' => 'price',
                'renderer' => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductPrice',
                'align' => 'right'
        ));
 
        $this->addColumn('qty', array(
            'header'=> Mage::helper('catalog')->__('Qty'),
            'index' => 'entity_id',
            'type'	=> 'number',
            'align'	=> 'center',
            'renderer' => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductStock',
            'filter' => false,
            'sortable' => false
        ));
		/** quickNdirty **/
		/*        $this->addColumn('visibility',
					array(
						'header'=> Mage::helper('catalog')->__('Visibility'),
						'width' => '70px',
						'index' => 'visibility',
						'type'  => 'options',
						'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
				));
		
				$this->addColumn('status',
					array(
						'header'=> Mage::helper('catalog')->__('Status'),
						'width' => '70px',
						'index' => 'status',
						'type'  => 'options',
						'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
				));
		*/
            
        $this->addColumn('action', array(
            'header' => Mage::helper('sales')->__('Action'),
            'renderer'  => 'MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_SelectProduct',
            'index' => 'content',
            'filter' => false,
            'align' => 'center'
        ));
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array('_current'=>true));
    }

}
