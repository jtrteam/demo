<?php

class Medma_Avatar_Block_Adminhtml_Renderer_Avatar extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

   public function render(Varien_Object $row)
    {
        if($row->getData($this->getColumn()->getIndex()) != NULL ){
			$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) 
                    . 'medma_avatar/' 
                    . $row->getData($this->getColumn()->getIndex());
		} else {
			$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) 
                    . 'medma_avatar/' 
                    . 'no_avatar.jpg';
		}
		$html = '<img ';
        $html .= 'src="' . $path . '"';
        $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '" width= "100" height="100" />';
        return $html;
    }

   
}
