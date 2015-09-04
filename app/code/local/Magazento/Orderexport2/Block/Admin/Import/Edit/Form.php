<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form data
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('import_general', array(
            'legend' => Mage::helper('core')->__('Import Orders')
        ));

        $fieldset->addField('xml_file', 'file', array(
            'label'     => Mage::helper('core')->__('XML file'),
            'name'      => 'xml_file',
            'value'     => 'Upload',
            'disabled'  => false,
            'required'  => true,
        ));


        $fieldset = $form->addFieldset('import_additional', array(
            'legend' => Mage::helper('core')->__('Additional Settings')
        ));

        $fieldset->addField('import_invoice', 'select', array(
            'name' => 'import_invoice',
            'label' => Mage::helper('orderexport2')->__('Import Invoice'),
            'title' => Mage::helper('orderexport2')->__('Import Invoice'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));

        $fieldset->addField('import_creditmemo', 'select', array(
            'name' => 'import_creditmemo',
            'label' => Mage::helper('orderexport2')->__('Import Credit Memo'),
            'title' => Mage::helper('orderexport2')->__('Import Credit Memo'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));

        $fieldset->addField('import_shipment', 'select', array(
            'name' => 'import_shipment',
            'label' => Mage::helper('orderexport2')->__('Import Shipment'),
            'title' => Mage::helper('orderexport2')->__('Import Shipment'),
            'required' => true,
            'value' => 1,
            'options' => array(
                '1' => Mage::helper('orderexport2')->__('Yes'),
                '0' => Mage::helper('orderexport2')->__('No'),
            ),
        ));
        

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}