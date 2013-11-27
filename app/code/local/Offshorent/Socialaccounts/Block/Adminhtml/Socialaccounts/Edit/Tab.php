<?php 

/**
 * Adminhtml customer action tab
 *
 */
class Offshorent_Socialaccounts_Block_Adminhtml_Socialaccounts_Edit_Tab extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{


   protected function _beforeToHtml()
    {
		//parent::_beforeToHtml();
		$this->addTab('socialaccounts', array(
            'label'     => Mage::helper('customer')->__('Social Accounts'),
            'content'   => $this->getLayout()->createBlock('socialaccounts/adminhtml_socialaccounts_edit_tab_social')->initForm()->toHtml(),
			'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));
		
	  return parent::_beforeToHtml();
    }
}
?>