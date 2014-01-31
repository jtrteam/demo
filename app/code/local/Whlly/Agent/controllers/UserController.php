<?php

class Whlly_Agent_UserController extends Mage_Core_Controller_Front_Action
{
   
  
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    
    public function preDispatch()
    {
        parent::preDispatch();
		
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        $openActions = array(
            'create'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
		
		$membership = Mage::getSingleton('customer/session')->getCustomer()->getMembership();
		if(($membership != 1) || (!Mage::getStoreConfig('agent/genaral/enable'))):
			$this->_redirectUrl($this->_getUrl('customer/account'));
		endif;
    }

  
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('agent/account_customer')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('Agent - Customers'));
        $this->renderLayout();
    }

   
    public function newAction() {
		$this->_forward('edit');
	}

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        $session = $this->_getSession(); 
		$session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/new', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
				$customer->setConfirmation(null);
				$customer->setGroupId(Mage::getSingleton('customer/session')->getCustomerGroupId()); 
				$customer->setStatus(1); 
				$customer->setMembership(0);                 
                $customer->save();
				 $this->_getSession()->addSuccess($this->__('The account information has been saved.'));

                $this->_redirect('agent/account');
                return;
				
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $message = $this->__('There is already an account with this email address.');
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save the customer.'));
        }
        $errUrl = $this->_getUrl('*/*/new', array('_secure' => true));
        $this->_redirectError($errUrl);
    }

   
    /**
     * Get Customer Model
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
		if($this->getRequest()->getParam('id')){
			$customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
        
		}else {
        	$customer = '';
		}
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }

    /**
     * Add session error method
     *
     * @param string|array $errors
     */
    protected function _addSessionError($errors)
    {
        $session = $this->_getSession();
        $session->setCustomerFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($errorMessage);
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }
        return $errors;
    }

    /**
     * Get Customer Form Initalized Model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm($customer)
    {
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = $this->_getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);
        return $customerForm;
    }

    /**
     * Get Helper
     *
     * @param string $path
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }

    /**
     * Get App
     *
     * @return Mage_Core_Model_App
     */
    protected function _getApp()
    {
        return Mage::app();
    }

    /**
     * Dispatch Event
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _dispatchRegisterSuccess($customer)
    {
        Mage::dispatchEvent('customer_register_success',
            array('account_controller' => $this, 'customer' => $customer)
        );
    }

    /**
     * Gets customer address
     *
     * @param $customer
     * @return array $errors
     */
    protected function _getErrorsOnCustomerAddress($customer)
    {
        $errors = array();
        /* @var $address Mage_Customer_Model_Address */
        $address = $this->_getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = $this->_getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
            ->setEntity($address);

        $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
        $addressErrors = $addressForm->validateData($addressData);
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        $address->setId(null)
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        $addressForm->compactData($addressData);
        $customer->addAddress($address);

        $addressErrors = $address->validate();
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        return $errors;
    }

    /**
     * Get model by path
     *
     * @param string $path
     * @param array|null $arguments
     * @return false|Mage_Core_Model_Abstract
     */
    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }

    /**
     * Get model from registry by path
     *
     * @param string $path
     * @return mixed
     */
    protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }

   
    
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }

  
    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
		
		$custData = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
        $data = $custData->getCustomerFormData(true);
        $customer = $custData->getData();
        if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $customer->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Customer Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }

    /**
     * Change customer password action
     */
    public function editPostAction()
    {
        
        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getPost('user_id'))->getData();
            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
			
            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();
                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/account');
                return $this;
            }

            try {
				$customer->setConfirmation(null);
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

                $this->_redirect('agent/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('agent/account');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null)
    {
        return  $this->_getHelper('customer/address')->isVatValidationEnabled($store);
    }
	
	public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Order History'));

        if ($block = $this->getLayout()->getBlock('agent.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
		$this->getLayout()->getBlock('head')->setTitle($this->__('Order History'));
        $this->renderLayout();
    }
	
	public function orderViewAction()
    {
       $this->_viewAction();
    }
	
	 protected function _viewAction()
    {
        if (!$this->_loadValidOrder()) {
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');     
		$this->getLayout()->getBlock('head')->setTitle($this->__('Customer Order'));   
        $this->renderLayout();
    }

    
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id');
        }
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }

        $order = Mage::getModel('sales/order')->load($orderId);
		Mage::register('current_order', $order);
		return true;
        
    }

   
	public function newOrderAction()
    {
		$this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('New Order'));
		$this->renderLayout();       
    }

	
	
}
