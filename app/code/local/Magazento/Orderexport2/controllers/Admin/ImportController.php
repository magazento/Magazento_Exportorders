<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Admin_ImportController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/orderexport2')
            ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2'), Mage::helper('orderexport2')->__('orderexport2'))
            ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2 Items'), Mage::helper('orderexport2')->__('orderexport2 Items'));
        return $this;
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['xml_file']['name']) && $_FILES['xml_file']['name'] != '') {
                try {
                    $xmlFile = $_FILES["xml_file"]["tmp_name"];
                } catch (Exception $e) {
                    echo $e;
                    exit();
                }
            }

            $result = Mage::getModel('orderexport2/import')->importFromFile($xmlFile,
                $data['import_invoice'],
                $data['import_creditmemo'],
                $data['import_shipment']
            );

            if ($result['errors']) Mage::getSingleton('adminhtml/session')->addError($result['errors']);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderexport2')->__('Orders imported: %s', $result['total']));

            $this->_redirect('*/admin_item/index');
        }
    }

    public function indexAction()
    {
        $this->loadLayout(array('default', 'editor'))
            ->_setActiveMenu('system/orderexport2');

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('orderexport2/admin_import_edit'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('orderexport2/item');
    }

}