<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Block_Admin_Item_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
       
        parent::__construct();
        $this->setId('related');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('sales/order')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    protected function _prepareColumns() {
         $this->addColumn('in_related', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name'=> 'related_list[]',
            'values'    => $this->_getSelectedItems(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Customer E-mail'),
            'index' => 'customer_email',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));


        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));



        return parent::_prepareColumns();
    }

  public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/relatedGrid', array('_current' => true));
    }


  protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_related') {
            $relatedIds = $this->_getSelectedItems();
            if (empty($relatedIds)) {
                $relatedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$relatedIds));
            } else {
                if($relatedIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$relatedIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _getSelectedItems()
    {
//        var_dump($this->getRequest()->getPost());
//        exit();
//        $relatedItems = $this->getRequest()->getPost('products', null);
//
//        if (!is_array($relatedItems)) {
            $id = Mage::app()->getFrontController()->getRequest()->get('item_id');
            $model = Mage::getModel('orderexport2/item')->load($id);
            $relatedItems = $model->getData('related_id');
            
//        }
//        var_dump($relatedItems);
        
        return $relatedItems;
    }    
 
}

?>
