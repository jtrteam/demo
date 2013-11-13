<?php

class Medma_Avatar_Block_Adminhtml_Renderer_Avatar extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

   public function render(Varien_Object $row)
    {
        $html = '<img ';
        $html .= 'id="' . $this->getColumn()->getId() . '" ';
        $html .= 'src="' . $row->getData($this->getColumn()->getIndex()) . '"';
        $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }

   
}
