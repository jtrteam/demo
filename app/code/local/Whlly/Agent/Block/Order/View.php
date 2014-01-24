<?php

class Whlly_Agent_Block_Order_View extends Mage_Sales_Block_Order_View
{

   public function getBackUrl()
    {
        return Mage::getUrl('agent/account');
    }


}
