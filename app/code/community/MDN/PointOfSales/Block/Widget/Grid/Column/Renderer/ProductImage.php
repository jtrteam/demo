<?php

class MDN_PointOfSales_Block_Widget_Grid_Column_Renderer_ProductImage
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        $url = Mage::getBaseUrl('media') . DS . $val;

        $out = $val. '<center><a href="'.$_url.'" target="_blank" id="imageurl">';
        $out .= "<img src=". $url ." width='60px' ";
        $out .=" />";
        $out .= '</a></center>';

        return $out;
    }

}