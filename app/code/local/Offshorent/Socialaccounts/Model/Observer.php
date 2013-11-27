<?php
class Offshorent_Socialaccounts_Model_Observer
{
	
	public function saveCustomerinfo(Varien_Event_Observer $observer) 
	{
					 $facebooklink = $_POST['facebook'];
					 $twitterlink = $_POST['twitter'];
					 $pinterestlink = $_POST['pinterest'];
					 $instagramlink = $_POST['instagram'];
					 $googlelink = $_POST['googleplus'];
					 
					 $customer = Mage::registry('current_customer');
					 $customer->setFacebook($facebooklink);
					 $customer->setTwitter($twitterlink);
					 $customer->setPinterest($pinterestlink);
					 $customer->setInstagram($instagramlink);
					 $customer->setGoogleplus($googlelink);
					 $customer->save();
					 
					 
					 
    }
	
}
?>