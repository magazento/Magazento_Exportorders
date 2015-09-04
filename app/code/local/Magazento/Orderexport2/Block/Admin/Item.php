<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    
    public function __construct()
    {
        
        $this->_controller = 'admin_item';
        $this->_blockGroup = 'orderexport2';
        $this->_headerText = Mage::helper('orderexport2')->__('Order Export Profiles');
        $this->_addButtonLabel = Mage::helper('orderexport2')->__('Add Profile');
        parent::__construct();

        $this->setTemplate('widget/grid/container.phtml');
    }
    
}
