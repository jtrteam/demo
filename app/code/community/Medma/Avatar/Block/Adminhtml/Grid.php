<?php

class Medma_Avatar_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    public function setCollection($collection)
	{
		$collection->addAttributeToSelect('medma_avatar');                   
		parent::setCollection($collection);
	}
    protected function _prepareColumns()
    {
		parent::_prepareColumns();
		
       	$this->addColumn('medma_avatar', array(
			'header'	=> Mage::helper('customer')->__('Photo'),
			'index'		=> 'medma_avatar',
			'renderer'  => 'avatar/adminhtml_renderer_avatar',
			'width'		=> 100
		));
        return parent::_prepareColumns();
    }

   
}
