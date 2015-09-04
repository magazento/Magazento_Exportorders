<?php
/*
* @category   Magazento
* @package    Magazento_Exportorders2
* @author     Ivan Proskuryakov
* @copyright  Copyright (c) 2014 Magazeto. (http://www.magazento.com)
* @license    Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
*/

class Magazento_Orderexport2_Model_Import
{
    private $XML = null;
    private $errors = null;
    private $customerId = null;
    private $orderModel = null;
    private $_orderedItems = null;


    /*
     * Customer Information
     */
    protected function importCustomer($order)
    {

        $customerModel = Mage::getModel('customer/customer')->setWebsiteId($order->Customer->website_id)->loadByEmail($order->Customer->email);
        if (!$customerModel->getId()) {
            $customer = json_decode(json_encode($order->Customer));
            unset($customer->entity_id);
            unset($customer->default_billing);
            unset($customer->default_shipping);
            unset($customer->increment_id);
            foreach ($customer as $k => $v) {
                $customerModel->setData($k, (string)$v);
            }
            $customerModel->save();
        }
        $this->customerId = $customerModel->getId();
    }

    /*
     * Invoice Information
     */
    protected function importInvoice($order)
    {

        foreach ($order->Invoices->children() as $invoice) {

            unset($invoice->entity_id);
            unset($invoice->increment_id);
            $invoice->order_id = $this->orderModel->getId();
            $invoice->customer_id = $this->customerId;
            // Vars
            $invoiceModel = Mage::getModel('sales/order_invoice');
            $invoiceModel->setOrder($this->orderModel);

            foreach ($invoice as $k => $v) {
                $invoiceModel->setData($k, (string)$v);
            }
            $invoiceModel->save();

            // Comments
            foreach ($invoice->Comments->children() as $comment) {
                $commentModel = Mage::getModel('sales/order_invoice_comment');
                unset($comment->entity_id);
                $comment->parent_id = $invoiceModel->getId();

                foreach ($comment as $k => $v) {
                    $commentModel->setData($k, (string)$v);
                }
                $invoiceModel->addComment($commentModel);
            }

            // Items
            $i = 0;
            foreach ($invoice->Items->children() as $item) {
                $itemModel = Mage::getModel('sales/order_invoice_item');
                unset($item->entity_id);
                $item->order_item_id = $this->_orderedItems[0];
                $item->parent_id = $invoiceModel->getId();

                foreach ($item as $k => $v) {
                    $itemModel->setData($k, (string)$v);
                }
                $itemModel->save();
                $invoiceModel->addItem($itemModel);
                $i++;
            }

            $invoiceModel->save();
        }

    }

    /*
     * CreditMemo Information
     */
    protected function importCreditMemo($order)
    {
        foreach ($order->CreditMemos->children() as $creditMemo) {
            unset($creditMemo->entity_id);
            unset($creditMemo->increment_id);
            $creditMemo->order_id = $this->orderModel->getId();
            $creditMemo->customer_id = $this->customerId;
            // Vars
            $creditMemoModel = Mage::getModel('sales/order_creditmemo');
            $creditMemoModel->setOrder($this->orderModel);

            foreach ($creditMemo as $k => $v) {
                $creditMemoModel->setData($k, (string)$v);
            }

            // Items
            $i = 0;
            foreach ($creditMemo->Items->children() as $item) {
                $itemModel = Mage::getModel('sales/order_creditmemo_item');
                unset($item->entity_id);
                $item->order_item_id = $this->_orderedItems[0];
                $itemModel->setCreditmemo($creditMemoModel);

                foreach ($item as $k => $v) {
                    $itemModel->setData($k, (string)$v);
                }
//                        $itemModel->save();
                $creditMemoModel->addItem($itemModel);
                $i++;
            }

//                    $creditMemoModel->save();

            // Comments
            foreach ($creditMemo->Comments->children() as $comment) {
                $commentModel = Mage::getModel('sales/order_creditmemo_comment');
                unset($comment->entity_id);
//                        $comment->parent_id = $creditMemoModel->getId();
                $commentModel->setCreditmemo($creditMemoModel);

                foreach ($comment as $k => $v) {
                    $commentModel->setData($k, (string)$v);
                }
                $creditMemoModel->addComment($commentModel);
            }

            $creditMemoModel->save();
        }

    }


    /*
     * Shipment Information
     */
    protected function importShipment($order)
    {

        foreach ($order->Shipments->children() as $shipment) {
            unset($shipment->entity_id);
            unset($shipment->increment_id);
            $shipment->order_id = $this->orderModel->getId();
            $shipment->customer_id = $this->customerId;


            // Vars
            $shipmentModel = Mage::getModel('sales/order_shipment');
            $shipmentModel->setOrder($this->orderModel);
            foreach ($shipment as $k => $v) {
                $shipmentModel->setData($k, (string)$v);
            }

            // Items
            $i = 0;
            foreach ($shipment->Items->children() as $item) {
                $itemModel = Mage::getModel('sales/order_shipment_item');
                unset($item->entity_id);
                $item->order_item_id = $this->_orderedItems[0];
                $itemModel->setShipment($shipmentModel);

                foreach ($item as $k => $v) {
                    $itemModel->setData($k, (string)$v);
                }
//                        $itemModel->save();
                $shipmentModel->addItem($itemModel);
                $i++;
            }
//                    $shipmentModel->save();

            // Comments
            foreach ($shipment->Comments as $comment) {
                $commentModel = Mage::getModel('sales/order_shipment_comment');
                unset($comment->entity_id);
                $commentModel->setShipment($shipmentModel);
//                        $comment->parent_id = $shipmentModel->getId();
                foreach ($comment as $k => $v) {
                    $commentModel->setData($k, (string)$v);
                }
                $shipmentModel->addComment($commentModel);
            }

            $shipmentModel->save();
        }
    }


    /*
     * Import Items
     */
    public function importFromFile($xmlFile, $importInvoice = true, $importCreditmemo = true, $importShipment = true)
    {
        $total = 0;
        $xmlContents = file_get_contents($xmlFile);
        $this->XML = simplexml_load_string($xmlContents);

        foreach ($this->XML as $order) {

            $total++;
            $this->orderModel = Mage::getModel('sales/order');

            // Customer
            $this->importCustomer($order);

//                var_dump($this->customerId);
//                exit();


            // Order Values
            $orderValues = json_decode(json_encode($order->OrderValues));
            unset($orderValues->entity_id);
            unset($orderValues->increment_id);
            $orderValues->customer_id = $this->customerId;
            foreach ($orderValues as $k => $v) {
                $this->orderModel->setData($k, (string)$v);
            }

            // Payment Collection
            $payments = json_decode(json_encode($order->Payments));
            foreach ($payments as $payment) {
                unset($payment->entity_id);

                unset($payment->parent_id);
                $paymentModel = Mage::getModel('sales/order_payment');
                foreach ($payment as $k => $v) {
                    $paymentModel->setData($k, (string)$v);
                }
                $this->orderModel->addPayment($paymentModel);
            }

            // StatusHistory Collection
            $statusHistory = $order->StatusHistory;

            foreach ($statusHistory->Status as $history) {
                unset($history->entity_id);
                $history->parent_id = $this->orderModel->getId();

                $statusHistoryModel = Mage::getModel('sales/order_status_history');
                foreach ($history as $k => $v) {
                    $statusHistoryModel->setData($k, (string)$v);
                }
                $this->orderModel->addStatusHistory($statusHistoryModel);
            }

            // Shipping
            $shippingModel = Mage::getModel('sales/order_address');
            $shipping = json_decode(json_encode($order->Shipping));
            unset($shipping->entity_id);
            unset($shipping->parent_id);
            foreach ($shipping as $k => $v) {
                $shippingModel->setData($k, (string)$v);
            }
            $this->orderModel->setShippingAddress($shippingModel);


            // Billing
            $billingModel = Mage::getModel('sales/order_address');
            $billing = json_decode(json_encode($order->Billing));
            unset($billing->entity_id);
            unset($billing->parent_id);
            foreach ($billing as $k => $v) {
                $billingModel->setData($k, (string)$v);
            }
            $this->orderModel->setBillingAddress($billingModel);

            $this->orderModel->save();

            // Items Collection
            $this->_orderedItems = array();
            $orderItems = $order->OrderItems;
            foreach ($orderItems->Item as $item) {

                $orderItemModel = Mage::getModel('sales/order_item');

                unset($item->item_id);
                unset($item->parent_item_id);
                unset($item->quote_item_id);
                $item->order_id = $this->orderModel->getId();

                foreach ($item as $k => $v) {
                    $orderItemModel->setData($k, (string)$v);
                }
                $orderItemModel->setOrder($this->orderModel);
                $orderItemModel->save();
                $this->_orderedItems[] = $orderItemModel->getId();
            }
            $this->orderModel->save();

            /*
             *  Invoice, CreditMemo and Shipment Collection
             */
            if ($importInvoice) $this->importInvoice($order);
            if ($importCreditmemo) $this->importCreditMemo($order);
            if ($importShipment) $this->importShipment($order);

        }

        $result = array(
            'total' => $total,
            'erorrs' => $this->errors,
        );
        return $result;

    }

}

