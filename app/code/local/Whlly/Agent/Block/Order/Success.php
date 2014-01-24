<?php

class Whlly_Agent_Block_Order_Success extends Mage_Checkout_Block_Cart
{

   public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }


}
