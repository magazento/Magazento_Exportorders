<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('orderexport2/item');
    }

    public function toOptionArray() {
        return $this->_toOptionArray('item_id', 'name');
    }
    
    public function addStoreFilter($store, $withAdmin = true) {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
                        array('item_store' => $this->getTable('orderexport2/item_store')),
                        'main_table.item_id = item_store.item_id',
                        array()
                )
                ->where('item_store.store_id in (?)', ($withAdmin ? array(0, $store) : $store));
        
        return $this;
    }

    public function addRelatedFilter($related) {

        $this->getSelect()->joinleft(
                        array('item_related' => $this->getTable('orderexport2/item_related')),
                        'main_table.item_id = item_related.item_id',
                        array()
                )
                ->distinct()                      
                ->where('item_related.related_id in (?) OR main_table.assign_relateds = 1', $related);

        return $this;
    }

    public function addNowFilter() {
        $now = Mage::getSingleton('core/date')->gmtDate();
        $where = "time_from < '" . $now . "' AND ((time_to > '" . $now . "') OR (time_to IS NULL))";
        $this->getSelect()->where($where);
    }

}