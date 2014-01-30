<?php
require_once 'Mage/Catalog/controllers/ProductController.php';
class Whlly_Agent_ProductController extends Mage_Catalog_ProductController
{
	
	 /**
     * Product view action
     */
    public function viewAction()
    {
        // Get initial data from request
        $productId  = (int) $this->getRequest()->getParam('pid');
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');
        $params = new Varien_Object();
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

	
}