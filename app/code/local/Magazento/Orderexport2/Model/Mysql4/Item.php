<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('orderexport2/item', 'item_id');
    }

   protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        if ($object->getTimeFrom()) {
            $object->setTimeFrom(Mage::app()->getLocale()->date($object->getTimeFrom(), $dateFormatIso));
        }
        if ($object->getTimeTo()) {
            $object->setTimeTo(Mage::app()->getLocale()->date($object->getTimeTo(), $dateFormatIso));
        }

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('item_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('orderexport2/item_store'), $condition);
        if ($object->getData('in_related')) $this->_getWriteAdapter()->delete($this->getTable('orderexport2/item_related'), $condition);
//        var_dump($object->getData('in_relateds') );
//        exit();        
        
        $relateds = $object->getData('related');
        
        foreach ((array) $relateds as $related) {
            if ($related == 0) continue;
            $relatedArray = array();
            $relatedArray['item_id'] = $object->getId();
            $relatedArray['related_id'] = $related;
            $this->_getWriteAdapter()->insert($this->getTable('orderexport2/item_related'), $relatedArray);
        }        
        
        //STORE
        if (!$object->getData('stores')) {
            $object->setData('stores', $object->getData('store_id'));
        }
        if (in_array(0, $object->getData('stores'))) {
            $object->setData('stores', array(0));
        }
        foreach ((array) $object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['item_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('orderexport2/item_store'), $storeArray);
        }

        
        
        return parent::_afterSave($object);
    }
    
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        
        //STORE
        $select = $this->_getReadAdapter()->select()
                        ->from($this->getTable('orderexport2/item_store'))
                        ->where('item_id = ?', $object->getId());
        
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
        
        //RELATED
        $select = $this->_getReadAdapter()->select()
                        ->from($this->getTable('orderexport2/item_related'))
                        ->where('item_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $relatedArray = array();
            foreach ($data as $row) {
                $relatedArray[] = $row['related_id'];
            }
            $object->setData('related_id', $relatedArray);
        }

        return parent::_afterLoad($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        $adapter = $this->_getReadAdapter();
        $adapter->delete($this->getTable('orderexport2/item_store'), 'item_id=' . $object->getId());
        $adapter->delete($this->getTable('orderexport2/item_related'), 'item_id=' . $object->getId());
    }

}