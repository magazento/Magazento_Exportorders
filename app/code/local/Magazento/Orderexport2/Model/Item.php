<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Model_Item extends Mage_Core_Model_Abstract
{
    const CACHE_TAG     = 'orderexport2_item';
    protected $_cacheTag= 'orderexport2_item';


    protected function _construct()
    {
        $this->_init('orderexport2/item');
    }



}
