<?php

/*
 * module => Medma_Avatar
 * upload the avatar 
 */

class Medma_Avatar_CustomerController extends Mage_Core_Controller_Front_Action {

    protected function getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    /*
     * show the upload form to the customer
     */

    public function formAction() {
        $this->loadLayout();

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('avatar')->__('My Avatar'));
        }

        $this->renderLayout();
    }

    public function uploadAction() {
        $errors = null;
        $session = Mage::getSingleton('core/session');
        $customerID = $this->getCustomerSession()->getCustomer()->getId();
        if ($this->getRequest()->isPost()
                && isset($_FILES['uploaded_file']['name'])
                && ($_FILES['uploaded_file']['name'] != '')
        ) {

            /*
             * * Below Code is the Upload Script
             */

            /* Get the uploaded file information */
            $name_of_uploaded_file = basename($_FILES['uploaded_file']['name']);

            //$name_of_uploaded_file = avatar_
            $type_of_uploaded_file = substr($name_of_uploaded_file, strrpos($name_of_uploaded_file, '.') + 1);

            //name of avatar with customer_ID appended
            $avatar_name = 'medma_avatar_' . $customerID . '.' . $type_of_uploaded_file;

            $size_of_uploaded_file = $_FILES["uploaded_file"]["size"] / 1024; //size in KBs
            /* ends */

            //Settings
            $max_allowed_file_size = 100; // size in KB
            $allowed_extensions = array("jpg", "jpeg", "gif", "bmp", 'png');


            //Validations

            $allowed_ext = false;
            for ($i = 0; $i < sizeof($allowed_extensions); $i++) {
                if (strcasecmp($allowed_extensions[$i], $type_of_uploaded_file) == 0) {
                    $allowed_ext = true;
                }
            }

            if ($size_of_uploaded_file > $max_allowed_file_size) {
                $errors .= "\n Size of file should be less than $max_allowed_file_size KB";
                $session->addError($this->__($errors));
            } elseif (!$allowed_ext) {

                //------ Validate the file extension -----//
                $errors .= "\n The uploaded file is not supported file type. " .
                        " Only the following file types are supported: " . implode(',', $allowed_extensions);
                $session->addError($this->__($errors));
            } else {

                //copy the temp. uploaded file to uploads folder
                $upload_folder = Mage::getBaseDir('media') . '/medma_avatar/';

                //Make a directory if not existent
                if (!is_dir(Mage::getBaseDir('media') . '/medma_avatar')) {
                    mkdir(Mage::getBaseDir('media') . '/medma_avatar');
                }

                $path_of_uploaded_file = $upload_folder . $avatar_name;

                $tmp_path = $_FILES["uploaded_file"]["tmp_name"];
                if (is_uploaded_file($tmp_path)) {
                    if (!copy($tmp_path, $path_of_uploaded_file)) {
                        $errors .= 'Error while copying the uploaded file';
                        $session->addError($this->__($errors));
                    } else {
                        $session->addSuccess($this->__('Avatar uploaded !'));
                    }
                }

                /**
                 * set avatar to the current customer
                 */
                $customer = Mage::getModel('customer/customer')->load($customerID);
                $customer->setMedmaAvatar($avatar_name);
                try {
                    $customer->save();
                } catch (Mage_Core_Exception $e) {
                    $session->addError($this->__('Some error occurred, please try later !'));
                }
            }
        } else {
            $errors .= 'Please select a file first !';
            $session->addError($this->__($errors));
        }
        $this->_redirectReferer();
    }

}

?>
