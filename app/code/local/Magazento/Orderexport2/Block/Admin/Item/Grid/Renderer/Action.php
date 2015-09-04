<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	    public function render(Varien_Object $row)
	    {
	
	         $actions[] = array(
	        	'url' => $this->getUrl('*/*/edit', array('item_id' => $row->getId())  ),
	        	'caption' => Mage::helper('orderexport2')->__('Edit')
	         );

	         $actions[] = array(
	        	'url' => $this->getUrl('*/admin_export/index', array('item_id' => $row->getId())  ),
	        	'caption' => Mage::helper('orderexport2')->__('Export')
	         );

	         $actions[] = array(
	        	'url' => $this->getUrl('*/*/delete', array('item_id' => $row->getId())),
	        	'caption' => Mage::helper('orderexport2')->__('Delete'),
	        	'confirm' => Mage::helper('orderexport2')->__('Are you sure you want to delete this item ?')
	         );
	
	        $this->getColumn()->setActions($actions);
	
	        return parent::render($row);
	    }
}
