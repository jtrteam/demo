<?php

class Whlly_Stores_Block_Adminhtml_Stores_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('storesGrid');
      $this->setDefaultSort('stores_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('stores/stores')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('stores_id', array(
          'header'    => Mage::helper('stores')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'stores_id',
      ));

      $this->addColumn('store_name', array(
          'header'    => Mage::helper('stores')->__('Store Name'),
          'align'     =>'left',
          'index'     => 'store_name',
      ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('stores')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('stores')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('stores_id');
        $this->getMassactionBlock()->setFormFieldName('stores');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('stores')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('stores')->__('Are you sure?')
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}