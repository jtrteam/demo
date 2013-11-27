<?php 

/**
 * Adminhtml customer action tab
 *
 */
class Offshorent_Socialaccounts_Block_Adminhtml_Socialaccounts_Edit_Tab_Action extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{


    public function __construct()
    {
		$customer = Mage::registry('current_customer');
		if($customer){
            $customerObj = Mage::getModel('customer/customer')->load($customer->getId());
            if($facebook = $customerObj->getFacebook()){
                $this->_facebook = $facebook; 
            }else{
                $this->_facebook = null;     
            }
			if($twitter = $customerObj->getTwitter()){
                $this->_twitter = $twitter; 
            }else{
                $this->_twitter = null;     
            }
			if($pinterest = $customerObj->getPinterest()){
                $this->_pinterest = $pinterest; 
            }else{
                $this->_pinterest = null;     
            }
			if($instagram = $customerObj->getInstagram()){
                $this->_instagram = $instagram; 
            }else{
                $this->_instagram = null;     
            }
			if($googleplus = $customerObj->getGoogleplus()){
                $this->_googleplus = $googleplus; 
            }else{
                $this->_googleplus = null;     
            }
        }

    }
     
	 public function getFacebook(){
        return $this->_facebook;
     }
	public function getFacebookLink()
	{
        $name = $this->getFacebook();
		 return $name;
    }
	
	public function getTwitter(){
        return $this->_twitter;
    }
	public function getTwitterLink()
	{
        $name = $this->getTwitter();
		 return $name;
    }
	
	public function getPinterest(){
        return $this->_pinterest;
    }
	public function getPinterestLink()
	{
        $name = $this->getPinterest();
		 return $name;
    }
	
	public function getInstagram(){
        return $this->_instagram;
    }
	public function getInstagramLink()
	{
        $name = $this->getInstagram();
		 return $name;
    }
	
	public function getGoogleplus(){
        return $this->_googleplus;
    }
	public function getGoogleplusLink()
	{
        $name = $this->getGoogleplus();
		 return $name;
    }
	
    public function getCustomtabInfo(){

        $customer = Mage::registry('current_customer');
        $customtab='My Custom tab Action Contents Here';
		return $customtab;
		}

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Social Accounts');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Action Tab');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

     /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'tags';
    }

}
?>