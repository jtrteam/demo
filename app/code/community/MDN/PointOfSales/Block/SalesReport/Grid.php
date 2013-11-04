<?php

class MDN_PointOfSales_Block_SalesReport_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('SalesReportGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->_parentTemplate = $this->getTemplate();
        $this->setDefaultLimit(200);
        $this->setSaveParametersInSession(true);

        /* $dateFormat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
          $dateFormat = str_replace('%', '', $dateFormat);

          $this->setDefaultFilter(array(
          'created_at' => array(
          'from' => $this->convertDateToObject(date($dateFormat)),
          'orig_from' => date($dateFormat),
          'to' => $this->convertDateToObject(date($dateFormat)),
          'orig_to' => date($dateFormat),
          'datetime' => false))
          ); */
    }

    protected function _prepareCollection() {
        //load collection depending of magento version

        if (Mage::helper('PointOfSales/MagentoVersionCompatibility')->ordersUseEavModel()) {
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
        } else {
            $collection = mage::getModel('sales/order')
                            ->getCollection()
                            ->addFieldToFilter('state', array('nin' => array('canceled')))
                            ->join('sales/order_address', '`sales/order_address`.entity_id=billing_address_id', array('billing_name' => "concat(firstname, ' ', lastname)"));
        }

        //add store filter
        $storeId = mage::helper('PointOfSales/User')->getStoreId();
        $collection->addFilter('store_id', $storeId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('increment_id', array(
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
            'index' => 'billing_name',
			'filter_index' => 'concat(firstname, \' \', lastname)'
         ));

        $this->addColumn('Products', array(
            'header' => Mage::helper('PointOfSales')->__('Products'),
            'renderer' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderProducts',
            'filter' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Filter_OrderProducts',
            'filter_index' => 'entity_id'
        ));

        $this->addColumn('payment_method', array(
            'header' => Mage::helper('PointOfSales')->__('Payment Method'),
            'renderer' => 'MDN_PointOfSales_Block_SalesReport_Widget_Grid_Column_Renderer_OrderPaymentMethod',
            'sortable' => false,
            'filter' => false
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
            'sortable' => false,
            'filter' => false
        ));

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
        return $this->getUrl('PointOfSales/SalesReport/Ajax', array('_current' => true, 'sales_report_date' => $this->getDate()));
    }

    //*************************************************************************************************************************************************
    //*************************************************************************************************************************************************
    //UI METHODS
    //*************************************************************************************************************************************************
    //*************************************************************************************************************************************************

    /**
     * Enter description here...
     *
     */
    public function getPrintUrl() {
        return $this->getUrl('PointOfSales/SalesReport/Print', array('date' => $this->getDate()));
    }

    /**
     * Convert timestamp to object
     *
     * @param <type> $timestamp
     */
    protected function convertDateToObject($date) {
        $locale = Mage::app()->getLocale();
        $dateObj = $locale->date(null, null, null, false);

        //set default timezone for store (admin)
        $dateObj->setTimezone(Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));


        //set begining of day
        $dateObj->setHour(00);
        $dateObj->setMinute(00);
        $dateObj->setSecond(00);

        //set date with applying timezone of store
        $dateObj->set($date, Zend_Date::DATE_SHORT, null);

        //convert store date to default date in UTC timezone without DST
        $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

        return $dateObj;
    }

}
