<?php

class Offshorent_AdminShare_Block_Widget_Grid_Column_Renderer_ProductShare extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        //collect information
        $product = $row;
       
        $productName  = $this->cleanTxt($row->getname());
        $productId    = $row->getId();
		
		$productData  = Mage::getModel("catalog/product")->load($productId);
		
		$storeId = $this->getRequest()->getParam('store');
		if ( $storeId > 0 ) {			
			$productUrl   = Mage::getModel('core/store')->load($storeId)->getUrl($productData->getUrlPath());
        }
		else {
			$storeId  = 1;
			$productUrl   = Mage::getModel('core/store')->load($storeId)->getUrl($productData->getUrlPath());
		}
		
		$productImage = $this->_getValue($row);
		
		
        $skinUrl = $this->getSkinUrl('images/adminshare/');
		
        $facebook_icon   = '<img src="' . $this->getSkinUrl('images/adminshare/facebook.png') . '">';
		$twitter_icon    = '<img src="' . $this->getSkinUrl('images/adminshare/twitter.png') . '">';
		$googleplus_icon = '<img src="' . $this->getSkinUrl('images/adminshare/google_plus.png') . '">';
		$pinterest_icon  = '<img src="' . $this->getSkinUrl('images/adminshare/pinterest.png') . '">';
		
		// Facebook
		
		$facebook   = '<a href="javascript:popWin(\'https://www.facebook.com/sharer/sharer.php?u='.urlencode($productUrl).'&t='.urlencode($productName).'\', \'facebook\', \'width=640,height=480,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes\');" title="'.$this->__('Share on Facebook').'">'.$facebook_icon.'</a>&nbsp;';
		
		// Twitter
		
		$twitter    = '<a href="javascript:popWin(\'http://twitter.com/home/?status='.urlencode($productName . ' (' . $productUrl . ')').'\', \'twitter\', \'width=640,height=480,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes\');" title="'.$this->__('Tweet').'">'.$twitter_icon.'</a>&nbsp;';
		
		// Google+
		
		$googleplus = '<a href="javascript:popWin(\'https://plus.google.com/share?url='.urlencode($productUrl).'\', \'google\', \'width=640,height=480,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes\');" title="'.$this->__('Share on Google Plus').'">'.$googleplus_icon.'</a>&nbsp;';
       
	   // Pinterest
	   
		$pinterest  = '<a href="javascript:popWin(\'https://pinterest.com/pin/create/button/?url='.urlencode($productUrl).'&media='.urlencode($productImage).'&description='.urlencode($productName).'\', \'pinterest\', \'width=640,height=480,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes\');" title="'.$this->__('Pin it').'">'.$pinterest_icon.'</a>';


        $retour = $facebook.$twitter.$googleplus.$pinterest;

        return $retour;
    }

    public function cleanTxt($txt) {
        return addslashes(str_replace('"', ' ', $txt));
    }
	
	protected function _getValue(Varien_Object $row)
    {      
        $val = $row->getData('image');
        $val = str_replace("no_selection", "", $val);
        $url = Mage::getBaseUrl('media') . 'catalog/product' . $val;
        return $url;
    }

}