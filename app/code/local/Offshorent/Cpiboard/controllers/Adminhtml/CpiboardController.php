<?php

class Offshorent_Cpiboard_Adminhtml_CpiboardController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cpiboard')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('CPI Definition'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
 
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('cpiboard/cpidefinition')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('cpiboard_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('cpiboard');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Definition Manager'), Mage::helper('adminhtml')->__('Definition Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Definition'), Mage::helper('adminhtml')->__('Definition'));

			$this->_addContent($this->getLayout()->createBlock('cpiboard/adminhtml_cpiboard_edit'))
				->_addLeft($this->getLayout()->createBlock('cpiboard/adminhtml_cpiboard_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cpiboard')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {	  			
			$model = Mage::getModel('cpiboard/cpidefinition');
			$data['store_id'] = 0;
			foreach ($data['store'] as $row):
				$data['store_id'] = $row;
			endforeach;
			if(!$this->getRequest()->getParam('id')){
				if($model->isDataExists($data) > 0){
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cpiboard')->__('This data is already avilable'));
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));			
			try {					
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cpiboard')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cpiboard')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('cpiboard/cpidefinition');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
}