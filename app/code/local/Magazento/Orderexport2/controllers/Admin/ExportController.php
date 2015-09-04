<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Admin_ExportController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('system/orderexport2')
                ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2'), Mage::helper('orderexport2')->__('orderexport2'))
                ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2 Items'), Mage::helper('orderexport2')->__('orderexport2 Items'))
        ;
        return $this;
    }


    public function indexAction() {

        if ($id = $this->getRequest()->getParam('item_id')) {

            try {
                $result = Mage::getModel('orderexport2/export')->exportItemsForProfile($id);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderexport2')->__('Orders exported: %s', $result['total']));
                Mage::getSingleton('adminhtml/session')->addSuccess($result['fileUrl']);
                $this->_redirect('*/admin_item/index');

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/admin_item/index');
                return;
            }
        }
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('orderexport2/item');
    }

}