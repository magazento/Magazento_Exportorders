<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Model_Export
{
    private $orderStatus = null;
    private $timeFrom = null;
    private $timeTo = null;
    private $orders = null;
    private $storeId = null;
    private $path = null;
    private $fileName = null;
    private $log = null;




    /*
     * Loads collection for manually selected items
     * */
    public function loadManualCollection($itemIds) {
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addAttributeToFilter('entity_id', array('in' => $itemIds));
        return $collection;
    }


    /*
     * Get Order list for selected Profile
     * */
    public function loadCollection($profile) {
        // Set Filters
        $this->path = $profile->getPath();
        $this->fileName = $profile->getFilename();
        $this->orderStatus = $profile->getOrderStatus();
        $this->timeFrom = $profile->getTimeFrom();
        $this->timeTo = $profile->getTimeTo();
        $this->storeId = $profile->getStoreId(0);
        if ($orders = $profile->getRelatedId()) {
            $this->orders = $orders ;
        }

        // Order Collection
        $collection = Mage::getModel('sales/order')->getCollection();

        if ($this->orderStatus) $collection->addAttributeToFilter('status', array('in' => $this->orderStatus));
        if ($this->timeFrom)    $collection->addAttributeToFilter('created_at', array('gteq' => $this->timeFrom));
        if ($this->timeTo)      $collection->addAttributeToFilter('created_at', array('lteq' => $this->timeTo));
        if ($this->storeId)     $collection->addAttributeToFilter('store_id', array('eq' => $this->storeId));

        return $collection;
    }

    /*
     * Export Items
     */
    public function exportItemsForProfile($profileId) {
        $profile = Mage::getModel('orderexport2/item')->load($profileId);
        $collection   = $this->loadCollection($profile);

        // Add manual selected items to our collection
        $manualCollection   = $this->loadManualCollection($profile->getData('related_id'));
        foreach ($manualCollection as $manualItem) {
            $found = false;
            foreach ($collection as $item) {
                if ($item->getId() == $manualItem->getId()) {
                    $found = true;
                    continue;
                }
            }
            if (!$found) $collection->addItem($manualItem);
        }




        $total = 0;
        $poArray = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Orders></Orders>');
        foreach ($collection as $order) {
            $total++;
            $this->log = 'Oderd Id: '.$order->getId().'<br/>';

            // Order Root
            $nodeOrder          = $poArray->addChild('Order');

            // Order Values
            $nodeValues       = $nodeOrder->addChild('OrderValues');
            foreach ($order->getData() as $k => $v) {
                $nodeValues->addChild($k, $v);
            }


            // Payment Collection
            $nodePayments       = $nodeOrder->addChild('Payments');
            $payments           = $order->getPaymentsCollection();
            foreach ($payments as $payment) {
                $nodePayment = $nodePayments->addChild('Payment');
                foreach ($payment->getData() as $k=>$v) {
                    $nodePayment->addChild($k, $v);
                }
            }

            // Invoice Collection
            if ($profile->getData('export_invoice')) {

                $nodeInvoices       = $nodeOrder->addChild('Invoices');
                $invoices           = $order->getInvoiceCollection();
                foreach ($invoices as $invoice) {
                    $nodeInvoice = $nodeInvoices->addChild('Invoice');

                    // Invoice Vars
                    foreach ($invoice->getData() as $k=>$v) {
                        $nodeInvoice->addChild($k, $v);
                    }

                    // Invoice Comments
                    $nodeComments = $nodeInvoice->addChild('Comments');
                    foreach ($invoice->getCommentsCollection() as $comment) {
                        $nodeComment = $nodeComments->addChild('Comment');
                        foreach ($comment->getData() as $k=>$v) {
                            $nodeComment->addChild($k, $v);
                        }
                    }
                    // Invoice Items
                    $nodeItems = $nodeInvoice->addChild('Items');
                    foreach ($invoice->getAllItems() as $item) {
                        $nodeItem = $nodeItems->addChild('Item');
                        foreach ($item->getData() as $k=>$v) {
                            $nodeItem->addChild($k, $v);
                        }
                    }
                }
            }

            // CreditMemo Collection
            if ($profile->getData('export_creditmemo')) {
                $nodeCreditMemos       = $nodeOrder->addChild('CreditMemos');
                $creditmemos           = $order->getCreditmemosCollection();
                foreach ($creditmemos as $creditmemo) {
                    $nodeCreditMemo = $nodeCreditMemos->addChild('CreditMemo');

                    // CreditMemo Vars
                    foreach ($creditmemo->getData() as $k=>$v) {
                        $nodeCreditMemo->addChild($k, $v);
                    }

                    // CreditMemo Comments
                    $nodeComments = $nodeCreditMemo->addChild('Comments');
                    foreach ($creditmemo->getCommentsCollection() as $comment) {
                        $nodeComment = $nodeComments->addChild('Comment');
                        foreach ($comment->getData() as $k=>$v) {
                            $nodeComment->addChild($k, $v);
                        }
                    }
                    // CreditMemo Items
                    $nodeItems = $nodeCreditMemo->addChild('Items');
                    foreach ($creditmemo->getAllItems() as $item) {
                        $nodeItem = $nodeItems->addChild('Item');
                        foreach ($item->getData() as $k=>$v) {
                            $nodeItem->addChild($k, $v);
                        }
                    }
                }
            }

            // Shipments Collection
            if ($profile->getData('export_shipment')) {
                $nodeShipments       = $nodeOrder->addChild('Shipments');
                $shipments           = $order->getShipmentsCollection();
                foreach ($shipments as $shipment) {
                    $nodeShipment = $nodeShipments->addChild('Shipment');

                    // Shipment Vars
                    foreach ($shipment->getData() as $k=>$v) {
                        $nodeShipment->addChild($k, $v);
                    }

                    // Shipment Comments
                    $nodeComments = $nodeShipment->addChild('Comments');
                    foreach ($shipment->getCommentsCollection() as $comment) {
                        $nodeComment = $nodeComments->addChild('Comment');
                        foreach ($comment->getData() as $k=>$v) {
                            $nodeComment->addChild($k, $v);
                        }
                    }
                    // Shipment Items
                    $nodeItems = $nodeShipment->addChild('Items');
                    foreach ($shipment->getAllItems() as $item) {
                        $nodeItem = $nodeItems->addChild('Item');
                        foreach ($item->getData() as $k=>$v) {
                            $nodeItem->addChild($k, $v);
                        }
                    }
                }
            }

            // StatusHistory Collection
            $nodeStatusHistory       = $nodeOrder->addChild('StatusHistory');
            $statusHistory           = $order->getStatusHistoryCollection();
            foreach ($statusHistory as $status) {
                $nodeStatus = $nodeStatusHistory->addChild('Status');
                foreach ($status->getData() as $k=>$v) {
                    $nodeStatus->addChild($k, $v);
                }
            }

            // Shipping
            if ($order->getShippingAddress()) {
                $nodeShipping       = $nodeOrder->addChild('Shipping');
                $shippingAddress    = $order->getShippingAddress();
                foreach ($shippingAddress->getData() as $k => $v) {
                    $nodeShipping->addChild($k, $v);
                }
            }


            // Billing
            $nodeShipping       = $nodeOrder->addChild('Billing');
            $billingAddress     = $order->getBillingAddress();
            foreach ($billingAddress->getData() as $k => $v) {
                $nodeShipping->addChild($k, $v);
            }

            // Customer
            $nodeCustomer       = $nodeOrder->addChild('Customer');
            $customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
            foreach ($customer->getData() as $k => $v) {
                $nodeCustomer->addChild($k, $v);
            }

            // Items Collection
            $nodeItems          = $nodeOrder->addChild('OrderItems');
            $items              = $order->getItemsCollection();
            foreach ($items AS $id => $item) {
                $nodeItem = $nodeItems->addChild('Item');
                foreach ($item->getData() as $k => $v) {
                    $nodeItem->addChild($k, $v);
                }
            }

        }

        $XML = $poArray->asXML();

        $fileName = $this->path.$this->fileName.'.xml';
        $file = Mage::getBaseDir().'/'.$fileName;
        $fileUrl = Mage::app()->getStore(0)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName;
        $fileUrl = '<a target="_blank" href="'.$fileUrl.'">'.$fileUrl.'</a>';

        file_put_contents($file,$XML);

        $result = array(
            'total'=>$total,
            'fileUrl'=>$fileUrl
        );

        return $result;

//        echo $this->log;
//        echo 'Total: '. $total.'<br/>';
//        echo '<a target="_blank" href="'.$fileUrl.'">'.$fileUrl.'</a>';

    }





}