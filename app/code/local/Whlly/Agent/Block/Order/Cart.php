<?php

class Whlly_Agent_Block_Order_Cart extends Mage_Checkout_Block_Cart
{

    public function getCheckoutUrl()
    {
		if($this->getRequest()->getParam('id')):
        	return $this->getUrl('agent/cart/ordercomplete', array('_secure'=>true,'id'=>$this->getRequest()->getParam('id')));
		endif;
    }

}
