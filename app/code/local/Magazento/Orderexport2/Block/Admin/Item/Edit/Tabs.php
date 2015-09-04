<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('orderexport2_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('orderexport2')->__('Order Export Profile'));
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
         

    protected function _beforeToHtml() {
        $this->addTab('form_section_item', array(
            'label' => Mage::helper('orderexport2')->__('General information'),
            'title' => Mage::helper('orderexport2')->__('General information'),
            'content' => $this->getLayout()->createBlock('orderexport2/admin_item_edit_tab_tabhoriz')->toHtml(),
        ));
        
        $this->addTab('related', array(
            'label' => Mage::helper('catalog')->__('Manual Orders'),
            'url' => $this->getUrl('*/*/related', array('_current' => true)),
            'class' => 'ajax',
        ));        

        return parent::_beforeToHtml();
    }

}