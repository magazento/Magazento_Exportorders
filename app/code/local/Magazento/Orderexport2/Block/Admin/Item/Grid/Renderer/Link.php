<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    

  public function render(Varien_Object $row)
    {
        $fileName = preg_replace('/^\//', '', $row->getPath() . $row->getFilename().'.xml');
        $url = $this->htmlEscape(Mage::app()->getStore(0)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName);

        if (file_exists(BP . DS . $fileName)) {
            return sprintf('<a target="_blank" href="%1$s">%1$s</a>', $url).'<br/>'.BP . DS . $fileName;
        }
        return $url;
    }    
}
