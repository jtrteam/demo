<?php
class Whlly_Sizematrix_Model_Source_Splitter extends Varien_Object
{
  public function toOptionArray()
  {
    return array(
      array('value' => 'name', 'label' => Mage::helper('sizematrix')->__('Name')),
      array('value' => 'sku', 'label' => Mage::helper('sizematrix')->__('Sku')),
    );
  }
}