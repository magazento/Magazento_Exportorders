<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Edit_Tab_Tabhoriz_Form extends Mage_Adminhtml_Block_Widget_Form {


    protected function _prepareForm() {
        $model = Mage::registry('orderexport2_item');

        $form = new Varien_Data_Form(array('id' => 'edit_form_item', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setHtmlIdPrefix('item_');

        $fieldset = $form->addFieldset('base_fieldset_automation', array('legend' => Mage::helper('orderexport2')->__('General settings'), 'class' => 'fieldset-wide'));

        if ($model->getItemId()) {
            $fieldset->addField('item_id', 'hidden', array(
                'name' => 'item_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('orderexport2')->__('Title'),
            'name'  => 'title',
            'required' => true,
        ));

        $fieldset->addField('filename', 'text', array(
            'label' => Mage::helper('orderexport2')->__('Filename'),
            'name'  => 'filename',
            'required' => true,
            'note'  => Mage::helper('orderexport2')->__('example: orders_june (without extension)'),
        ));

        $fieldset->addField('path', 'text', array(
            'label' => Mage::helper('orderexport2')->__('Path'),
            'name'  => 'path',
            'required' => true,
            'note'  => Mage::helper('orderexport2')->__('example: "export/" or "/" for base path (path must be writeable)'),
        ));



        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('orderexport2')->__('Export Filters'), 'class' => 'fieldset-wide'));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label'    => Mage::helper('orderexport2')->__('Store View'),
                'title'    => Mage::helper('orderexport2')->__('Store View'),
                'name'     => 'store_id',
                'required' => false,
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false)
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $fieldset->addField('time_from', 'date', array(
            'name' => 'time_from',
            'time' => true,
            'label' => Mage::helper('orderexport2')->__('From Time'),
            'title' => Mage::helper('orderexport2')->__('From Time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
        ));

        $fieldset->addField('time_to', 'date', array(
            'name' => 'time_to',
            'time' => true,
            'label' => Mage::helper('orderexport2')->__('To Time'),
            'title' => Mage::helper('orderexport2')->__('To Time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
        ));

//        $states = Mage::getSingleton('sales/order_config')->getStates();
//        $states = array_merge(array('' => ''), $states);

        $statuses = Mage::getResourceModel('sales/order_status_collection')
            ->toOptionArray();
        array_unshift($statuses, array('value' => '', 'label' => 'ALL'));

        $fieldset->addField('order_status', 'multiselect', array(
            'label'    => Mage::helper('orderexport2')->__('Order Status'),
            'title'    => Mage::helper('orderexport2')->__('Order Status'),
            'name'     => 'order_status[]',
            'required' => false,
            'values'   => $statuses
        ));






        $fieldset->addField('script_java', 'note', array(
            'text' => '<script type="text/javascript">
				            var inputDateFrom = document.getElementById(\'item_from_time\');
				            var inputDateTo = document.getElementById(\'item_to_time\');
            				inputDateTo.onchange=function(){dateTestAnterior(this)};
				            inputDateFrom.onchange=function(){dateTestAnterior(this)};


				            function dateTestAnterior(inputChanged){
				            	dateFromStr=inputDateFrom.value;
				            	dateToStr=inputDateTo.value;

				            	if(dateFromStr.indexOf(\'.\')==-1)
				            		dateFromStr=dateFromStr.replace(/(\d{1,2} [a-zA-Zâêûîôùàçèé]{3})[^ \.]+/,"$1.");
				            	if(dateToStr.indexOf(\'.\')==-1)
				            		dateToStr=dateToStr.replace(/(\d{1,2} [a-zA-Zâêûîôùàçèé]{3})[^ \.]+/,"$1.");

				            	fromDate= Date.parseDate(dateFromStr,"%e %b %Y %H:%M:%S");
				            	toDate= Date.parseDate(dateToStr,"%e %b %Y %H:%M:%S");

				            	if(dateToStr!=\'\'){
					            	if(fromDate>toDate){
	            						inputChanged.value=\'\';
	            						alert(\'' . Mage::helper('orderexport2')->__('You must set a date to value greater than the date from value') . '\');
					            	}
				            	}
            				}
            			</script>',
            'disabled' => true
        ));
        

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
