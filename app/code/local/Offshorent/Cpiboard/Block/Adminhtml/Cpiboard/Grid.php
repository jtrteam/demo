<?php



class Offshorent_Cpiboard_Block_Adminhtml_Cpiboard_Grid extends Mage_Adminhtml_Block_Widget_Grid

{

  public function __construct()

  {

      parent::__construct();

      $this->setId('cpiboardGrid');

      $this->setDefaultSort('def_id');

      $this->setDefaultDir('ASC');

      $this->setSaveParametersInSession(true);
	  

  }

  protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

  protected function _prepareCollection()
	  {
	
		  $collection = Mage::getModel('cpiboard/cpidefinition')->getCollection();
		  
		  /*-----Store Filter ------*/
		  
		  $store = $this->_getStore();
		  if ($store->getId()) {			
			$collection->addFieldToFilter('store_id',$store->getId());	
		  }else {
			$collection->addFieldToFilter('store_id',0);	
		  }
		  
		  /*-----Store Filter ------*/
	
		  $msa_cpi = Mage::getSingleton('core/resource')->getTableName('offshorent_cpi');      
	
		  $collection->getSelect()->joinLeft(array('cpi'=>$msa_cpi),'`main_table`.`cpi_id` = `cpi`.`cpi_id`',array('title')); 
		  
		  $this->setCollection($collection);
		  return parent::_prepareCollection();
	
	  }



  protected function _prepareColumns()

  {

      $this->addColumn('title', array(

          'header'    => Mage::helper('cpiboard')->__('CPI'),

          'align'     =>'left',

          'index'     => 'title',

      ));

	 $this->addColumn('year', array(

          'header'    => Mage::helper('cpiboard')->__('Year'),

          'align'     =>'left',

          'index'     => 'year',

      ));

	

	 $this->addColumn('jan', array(

          'header'    => Mage::helper('cpiboard')->__('1'),

          'align'     =>'left',

          'index'     => 'jan',

		  'width'     => '75px',

      ));

	  

	 $this->addColumn('feb', array(

          'header'    => Mage::helper('cpiboard')->__('2'),

          'align'     =>'left',

          'index'     => 'feb',

		  'width'     => '75px',

      ));

	  

	   $this->addColumn('mar', array(

          'header'    => Mage::helper('cpiboard')->__('3'),

          'align'     =>'left',

          'index'     => 'mar',

		  'width'     => '75px',

      ));

	   $this->addColumn('apr', array(

          'header'    => Mage::helper('cpiboard')->__('4'),

          'align'     =>'left',

          'index'     => 'apr',

		  'width'     => '75px',

      ));

	  $this->addColumn('may', array(

          'header'    => Mage::helper('cpiboard')->__('5'),

          'align'     =>'left',

          'index'     => 'may',

		  'width'     => '75px',

      ));

	  

	  $this->addColumn('jun', array(

          'header'    => Mage::helper('cpiboard')->__('6'),

          'align'     =>'left',

          'index'     => 'jun',

		  'width'     => '75px',

      ));

	  $this->addColumn('jul', array(

          'header'    => Mage::helper('cpiboard')->__('7'),

          'align'     =>'left',

          'index'     => 'jul',

		  'width'     => '75px',

      ));

	  $this->addColumn('aug', array(

          'header'    => Mage::helper('cpiboard')->__('8'),

          'align'     =>'left',

          'index'     => 'aug',

		  'width'     => '75px',

      ));

	  $this->addColumn('sep', array(

          'header'    => Mage::helper('cpiboard')->__('9'),

          'align'     =>'left',

          'index'     => 'sep',

		  'width'     => '75px',

      ));

	  $this->addColumn('oct', array(

          'header'    => Mage::helper('cpiboard')->__('10'),

          'align'     =>'left',

          'index'     => 'oct',

		  'width'     => '75px',

      ));

	  $this->addColumn('nov', array(

          'header'    => Mage::helper('cpiboard')->__('11'),

          'align'     =>'left',

          'index'     => 'nov',

		  'width'     => '75px',

      ));

	  $this->addColumn('dec', array(

          'header'    => Mage::helper('cpiboard')->__('12'),

          'align'     =>'left',

          'index'     => 'dec',

		  'width'     => '75px',

      ));

    

	  

        $this->addColumn('action',

            array(

                'header'    =>  Mage::helper('cpiboard')->__('Action'),

                'width'     => '100',

                'type'      => 'action',

                'getter'    => 'getId',

                'actions'   => array(

                    array(

                        'caption'   => Mage::helper('cpiboard')->__('Edit'),

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



  public function getRowUrl($row)

  {

      return $this->getUrl('*/*/edit', array('id' => $row->getId()));

  }



}