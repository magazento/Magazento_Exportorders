<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'item_id';
        $this->_controller = 'admin_import';
        $this->_blockGroup = 'orderexport2';

        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('delete');


        $this->_updateButton('save', 'label', Mage::helper('orderexport2')->__('Import File'));

    }

    public function getHeaderText()
    {
        return Mage::helper('orderexport2')->__("Import from XML file");
    }

}
