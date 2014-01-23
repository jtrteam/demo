<?php
class Whlly_Agent_Block_Form_User extends Mage_Directory_Block_Data
{
    /**
     * Address instance with data
     *
     * @var Mage_Customer_Model_Address
     */
    protected $_address;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Create New Customer Account'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
		if($this->isEdit()){
			return $this->helper('agent/user')->getEditPostUrl();
		}else {
			return $this->helper('agent/user')->getRegisterPostUrl();
		}
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->helper('customer')->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
		$userId = $this->getRequest()->getParam('id');
		if (is_null($data)) {
			if ($userId) {
				$customer = Mage::getModel('customer/customer')->load($userId);
				$formData = $customer->getData();
				if( $customer->getDefaultBilling()){
					$address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
					$formData['company'] = $address->getCompany();
					$formData['telephone'] = $address->getTelephone();
					$formData['street'] = $address->getStreet();
					$formData['city'] = $address->getCity();
					$formData['city'] = $address->getCity();
					$formData['region_id'] = $address->getRegionId();
					$formData['region'] = $address->getRegion();
					$formData['postcode'] = $address->getPostcode();
					$formData['country_id'] = $address->getCountryId();
				}
			 } else {
				$formData = Mage::getSingleton('customer/session')->getCustomerFormData(true);
			 }
			 $data = new Varien_Object();
            if ($formData) {
				$data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return int
     */
    public function getRegion()
    {
        if (false !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } else if (false !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }

    /**
     *  Newsletter module availability
     *
     *  @return boolean
     */
    public function isNewsletterEnabled()
    {
        return Mage::helper('core')->isModuleOutputEnabled('Mage_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = Mage::getModel('customer/address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param Mage_Customer_Model_Form $form
     * @return Mage_Customer_Block_Form_Register
     */
    public function restoreSessionData(Mage_Customer_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }
	
	 public function isEdit()
    {
       if($this->getRequest()->getParam('id') > 0 ){
		   return true;
	   }
	   
	    return false;
    }
}