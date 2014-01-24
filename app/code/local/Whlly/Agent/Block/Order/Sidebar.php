<?php

class Whlly_Agent_Block_Order_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
	public function getItemRenderer($type)
	{
		if (!isset($this->_itemRenders[$type])) {
			$type = 'default';
		}
		if (is_null($this->_itemRenders[$type]['blockInstance'])) {
			$this->_itemRenders[$type]['blockInstance'] = $this->getLayout()
			->createBlock($this->_itemRenders[$type]['block'])
			->setTemplate('agent/order/sidebar/default.phtml')
			->setRenderedBlock($this);
		}

		return $this->_itemRenders[$type]['blockInstance'];
	}
}