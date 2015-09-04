<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Admin_ItemController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('system/orderexport2')
            ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2'), Mage::helper('orderexport2')->__('orderexport2'))
            ->_addBreadcrumb(Mage::helper('orderexport2')->__('orderexport2 Items'), Mage::helper('orderexport2')->__('orderexport2 Items'))
        ;
        return $this;
    }

    /**
     * Related part
     */    
    public function relatedAction() {
        
        $this->loadLayout();
        $this->getLayout()->getBlock('related.grid');
        $this->renderLayout();
    }

    public function relatedgridAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock('related.grid');
        $this->renderLayout();
    }

    public function indexAction() {
        $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('orderexport2/admin_item'))
                ->renderLayout();
    }


    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        
        $id = $this->getRequest()->getParam('item_id');
        
        if (Mage::helper('orderexport2')->versionUseAdminTitle()) {
            $this->_title($this->__('orderexport2'));
        }

        $model = Mage::getModel('orderexport2/item');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orderexport2')->__('This item no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
        Mage::register('orderexport2_item', $model);
        
        
        $this->loadLayout(array('default', 'editor'))
            ->_setActiveMenu('system/orderexport2');

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true)
            ->addItem('js', 'magazento_orderexport2/adminhtml/tabs.js');        
        
        $this-> _addBreadcrumb($id ? Mage::helper('orderexport2')->__('Edit Item') : Mage::helper('orderexport2')->__('New Item'), $id ? Mage::helper('orderexport2')->__('Edit Item') : Mage::helper('orderexport2')->__('New Item'))
                ->_addContent($this->getLayout()->createBlock('orderexport2/admin_item_edit')->setData('action', $this->getUrl('*/admin_item/save')))
                ->_addLeft($this->getLayout()->createBlock('orderexport2/admin_item_edit_tabs'))
                ->renderLayout();
    }


    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

//            var_dump($data);
//            exit();

            // Order Statuses
            $orderStatus = $this->getRequest()->getParam('order_status');
            $orderStatus = array_filter($orderStatus);
            $data['order_status'] = implode(',',$orderStatus);


//            var_dump($data);
//            exit();
            // Assigned items
            if (isset($data['related_list'])) {
                $data['related'] = $data['related_list'];
            }
            if (isset($data['in_related'])) $data['in_related'] = true;

            $model = Mage::getModel('orderexport2/item');
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderexport2')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('item_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('item_id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }



    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('item_id')) {
            try {
                $model = Mage::getModel('orderexport2/item');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderexport2')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('item_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orderexport2')->__('Unable to find a item to delete'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $itemIds = $this->getRequest()->getParam('massaction');
        if(!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orderexport2')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $mass = Mage::getModel('orderexport2/item')->load($itemId);
                    $mass->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('orderexport2')->__(
                        'Total of %d record(s) were successfully deleted', count($itemIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('orderexport2/item');
    }

    public function wysiwygAction() {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId
        ));
        $this->getResponse()->setBody($content->toHtml());
    }

}