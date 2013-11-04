<?php

class MDN_PointOfSales_Block_OtherSales_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('OtherSalesGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->_parentTemplate = $this->getTemplate();
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {
        //load collection depending of magento version
        switch (mage::helper('PointOfSales/MagentoVersionCompatibility')->getVersion()) {
            case '1.4.0':
            case '1.4.1':
            case '1.4.2':
            case '1.5.0':
            case '1.8.0':
            case '1.9.0':
                $collection = mage::getModel('sales/order')
                                ->getCollection()
                                ->addFieldToFilter('state', array('nin' => array('canceled')))
                                ->join('sales/order_address', '`sales/order_address`.entity_id=billing_address_id', array('billing_name' => "concat(firstname, ' ', lastname)"))
                                ->setOrder('entity_id', 'asc')
                ;
                break;
            default:
                $collection = Mage::getResourceModel('sales/order_collection')
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('state', array('nin' => array('canceled')))
                                ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
                                ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
                                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                                ->addExpressionAttributeToSelect('billing_name',
                                        'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
                                        array('billing_firstname', 'billing_lastname'));
                break;
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px'
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name'
        ));

        $this->addColumn('Products', array(
            'header' => Mage::helper('PointOfSales')->__('Products'),
            'renderer' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderProducts',
            'filter' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Filter_OrderProducts',
            'filter_index' => 'entity_id'
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
            'sortable' => false,
            'filter' => false
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased from (store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }
        
        $this->addColumn('details', array(
            'header' => Mage::helper('sales')->__('Details'),
            'index' => 'entity_id',
            'renderer' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderDetails',
            'align' => 'center',
            'filter' => false,
            'sortable' => false
        ));


        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    public function getGridUrl() {
        return $this->getUrl('PointOfSales/OtherSales/Ajax', array('_current' => true, 'sales_report_date' => $this->getDate()));
    }
  
}
