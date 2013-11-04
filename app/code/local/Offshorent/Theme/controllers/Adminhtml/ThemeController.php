<?php

class Offshorent_Theme_Adminhtml_ThemeController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('theme')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Change Your Store Theme'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	
	public function saveAction() {
		  if ($data = $this->getRequest()->getPost()) {
            $store_id = (int) $this->getRequest()->getParam('store_id');

            $installer = new Mage_Core_Model_Config();
            $theme = explode('/', $data['design']);
            if ($id) {
                $design->setId($id);
            }
            try {
				$installer->saveConfig('design/package/name', $theme[0], 'stores', $store_id);
				$installer->saveConfig('design/theme/template', $theme[1], 'stores', $store_id);
				$installer->saveConfig('design/theme/skin', $theme[1], 'stores', $store_id);
				$installer->saveConfig('design/theme/layout', $theme[1], 'stores', $store_id);
				$installer->saveConfig('design/theme/default', $theme[1], 'stores', $store_id);
				
				$page_key = 'home-'.$theme[1];
				
				$pageTitle = Mage::getModel('cms/page')->load($page_key, 'identifier')->getTitle();
				if($pageTitle){
				  $installer->saveConfig('web/default/cms_home_page', $page_key, 'stores', $store_id);
				}else{
				   $installer->saveConfig('web/default/cms_home_page', 'home', 'stores', $store_id);
				}

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The design change has been saved.'));
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_redirect('*/*/');
		
		
	}
 
	
}