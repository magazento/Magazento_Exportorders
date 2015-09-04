<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Edit_Tab_Tabhoriz_Additional extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        $model = Mage::registry('orderexport2_item');

        if (!$model->getId()) {
            $model->setData('export_invoice', 1);
            $model->setData('export_creditmemo', 1);
            $model->setData('export_shipment', 1);
        }


        $form = new Varien_Data_Form(array('id' => 'edit_form_item_additional', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset_additional', array('legend' => Mage::helper('orderexport2')->__('Additional Settings'), 'class' => 'fieldset-wide'));
        $fieldset->addField('export_invoice', 'select', array(
            'name' => 'export_invoice',
            'label' => Mage::helper('orderexport2')->__('Export Invoice'),
            'title' => Mage::helper('orderexport2')->__('Export Invoice'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));

        $fieldset->addField('export_creditmemo', 'select', array(
            'name' => 'export_creditmemo',
            'label' => Mage::helper('orderexport2')->__('Export Credit Memo'),
            'title' => Mage::helper('orderexport2')->__('Export Credit Memo'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));

        $fieldset->addField('export_shipment', 'select', array(
            'name' => 'export_shipment',
            'label' => Mage::helper('orderexport2')->__('Export Shipment'),
            'title' => Mage::helper('orderexport2')->__('Export Shipment'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
