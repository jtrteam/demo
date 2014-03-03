<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Whlly_Agent_CartController extends Mage_Checkout_CartController
{
	
	 public function indexAction() {
        parent::indexAction();
		$data = $this->getRequest()->getParams();
		if(Mage::helper('checkout/cart')->getCart()->getItemsCount()> 0):
			$this->_addDetaisToQuote($data);
		endif;
    }
	
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
					//$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
					$sidebar_block =  $this->getLayout()->createBlock('agent/order_sidebar')->setTemplate('agent/order/sidebar.phtml');//$this->getLayout()->getBlock('agent_order_sidebar');
					Mage::register('referrer_url', $this->_getRefererUrl());
					$sidebar = $sidebar_block->toHtml();
					//$response['toplink'] = $toplink;
					$response['sidebar'] = $sidebar;
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
	
	public function optionsAction(){
		$productId = $this->getRequest()->getParam('product_id');
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}
	
	public function updateDiscountAction()
	{
		$data = $this->getRequest()->getParams();
		if($data['discount']!= ''):
			$quote_id = Mage::getSingleton('checkout/session')->getQuoteId();		
			$apiDetails = Mage::getModel('api/server_handler')->login(Mage::getStoreConfig('agent/genaral/api_username'), Mage::getStoreConfig('agent/genaral/api_password'));		
			$resultCartCouponAdd = Mage::getModel('api/server_handler')->call($apiDetails, "cart_coupon.add", array($quote_id,$data['discount']));
			$shoppingCartTotals = Mage::getModel('api/server_handler')->call($apiDetails, "cart.totals", array($quote_id));
			$shoppingCartInfo = Mage::getModel('api/server_handler')->call($apiDetails, "cart.info", array($quote_id));
		endif;
		
		$this->_redirectUrl( Mage::getUrl('agent/cart',array('id'=>$data['id'])));
		
	}
	
	public function orderCompleteAction()
	{
		
		$quote_id = Mage::getSingleton('checkout/session')->getQuoteId();		
		$apiDetails = Mage::getModel('api/server_handler')->login(Mage::getStoreConfig('agent/genaral/api_username'), Mage::getStoreConfig('agent/genaral/api_password'));
		$orderId = Mage::getModel('api/server_handler')->call($apiDetails,"cart.order",array($quote_id, null, null));
		if($orderId){
			$this->_redirectUrl(Mage::getUrl('agent/cart/success',array('order_id' => $orderId)));
		} else {
			
			$this->_redirectUrl(Mage::getUrl('agent/cart/failure'));
		}
		
	}
	
	public function successAction()
	{		
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Order Success'));
        $this->renderLayout();
		
	}
	
	public function failureAction()
    {
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Order Failure'));
        $this->renderLayout();
	}
	
	 protected function _addDetaisToQuote($data)
    {
        $customerId = $data['id'];
		$customerData = Mage::getModel('customer/customer')->load($customerId);
        $customerAddressId = $customerData->getDefaultBilling();
        if ($customerAddressId):
			$addressBilling = Mage::getModel('customer/address')->load($customerAddressId);
		endif; 
		
		$quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
		
		$apiDetails = Mage::getModel('api/server_handler')->login(Mage::getStoreConfig('agent/genaral/api_username'), Mage::getStoreConfig('agent/genaral/api_password'));
		if(!$apiDetails):
		 $this->_getSession()
                ->addException($e, $this->__('Please add Api user.'));
		 $this->_redirectUrl( Mage::getUrl('agent/user/neworder',array('id'=>$data['id'])));
		endif;
		$customer = array('entity_id' => $customerId,'mode' => 'customer');
		try { 
				$resultCustomerSet = Mage::getModel('api/server_handler')->call($apiDetails, 'cart_customer.set', array( $quote_id, $customer) );
				$resultCustomeraddrs = Mage::getModel('api/server_handler')->call($apiDetails, 'customer_address.list', $customerId);
				foreach ($resultCustomeraddrs as $_address):
						if($_address['is_default_billing']=='1'):
							$arrAddresses[0]= array("mode" => "billing","address_id" => $_address['customer_address_id']);
						endif;
						if($_address['is_default_shipping']=='1'):
							$arrAddresses[1]= array("mode" => "shipping","address_id" => $_address['customer_address_id']);
						endif;
				endforeach;
				
				 $resultCustomerAddresses = Mage::getModel('api/server_handler')->call($apiDetails, "cart_customer.addresses", array($quote_id, $arrAddresses));
				 
				 $resultShippingList = Mage::getModel('api/server_handler')->call($apiDetails,"cart_shipping.list", array($quote_id));
				
				 foreach($resultShippingList as $key=>$value):
					if($value['code']=='freeshipping_freeshipping'):
						$resultShippingMethod = Mage::getModel('api/server_handler')->call($apiDetails, "cart_shipping.method", array($quote_id, 'freeshipping_freeshipping'));
					elseif($value['code']=='flatrate_flatrate'):
						$resultShippingMethod = Mage::getModel('api/server_handler')->call($apiDetails, "cart_shipping.method", array($quote_id, 'flatrate_flatrate'));
					else:
						$resultShippingMethod = Mage::getModel('api/server_handler')->call($apiDetails, "cart_shipping.method", array($quote_id, 'tablerate_bestway'));
					endif;
				 endforeach;
				if($data['discount']!= ''):
					$resultCartCouponAdd = Mage::getModel('api/server_handler')->call($apiDetails, "cart_coupon.add", array($quote_id,$data['discount']));
				endif;
				
				$paymentMethod = array("method" => "checkmo");
				$resultPaymentMethod = Mage::getModel('api/server_handler')->call($apiDetails, "cart_payment.method", array($quote_id, $paymentMethod));
				$shoppingCartTotals = Mage::getModel('api/server_handler')->call($apiDetails, "cart.totals", array($quote_id));
				$shoppingCartInfo = Mage::getModel('api/server_handler')->call($apiDetails, "cart.info", array($quote_id));
			} catch (Exception $e) {
				 $this->_getSession()
                ->addException($e, $this->__('Checkout process cannot process due to some errors.'));
				$this->_redirectUrl( Mage::getUrl('agent/user/neworder',array('id'=>$data['id'])));
			}
		return;
    }
	
}