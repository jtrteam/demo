<?php
class Offshorent_Socialaccounts_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function savelinksAction()
    {
		
	  if(Mage::getSingleton('customer/session')->isLoggedIn()) 
		 {
				$session = Mage::getSingleton('core/session');
                $customerData = Mage::getSingleton('customer/session')->getCustomer();
                $customerID = $customerData->getId();
       
		      if ($this->getRequest()->isPost()) 
			      {
					 $facebooklink = $_POST['facebook'];
					 $twitterlink = $_POST['twitter'];
					 $pinterestlink = $_POST['pinterest'];
					 $instagramlink = $_POST['instagram'];
					 $googlelink = $_POST['googleplus'];
			         $customer = Mage::getModel('customer/customer')->load($customerID);
                     $customer->setFacebook($facebooklink);
					 $customer->setTwitter($twitterlink);
					 $customer->setPinterest($pinterestlink);
					 $customer->setInstagram($instagramlink);
					 $customer->setGoogleplus($googlelink);
                  try 
				    {
                       $customer->save();
					  // echo "Customer information saved successfully";
                    } 
				    catch (Mage_Core_Exception $e) 
					  {
                         $session->addError($this->__('Some error occurred, please try later !'));
                      }
		                 
						 
		         }
		 }
		 $this->_redirect('customer/account/login');
	}
	
}