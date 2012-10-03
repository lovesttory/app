<?php
class SM_XPos_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{

    public function recollectCart(){
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()
                ->collectTotals()
                ->save();
        }
        $this->setRecollect(true);
        return $this;
    }
    
    /**
     * Update quantity of order quote items
     *
     * @param   array $data
     * @return  SM_XPos_Model_Adminhtml_Sales_Order_Create
     */    
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            try {
                foreach ($data as $itemId => $info) {
//                    var_dump($itemId);var_dump($info);die;
                    if (!empty($info['configured'])) {
                        $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                        $itemQty = (float)$item->getQty();
                    } else {
                        $item       = $this->getQuote()->getItemById($itemId);
                        $itemQty    = (float)$info['qty'];
                    }
                    if ($item) {
                        if ($item->getProduct()->getStockItem()) {
                            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                                $itemQty = (int)$itemQty;
                            } else {
                                $item->setIsQtyDecimal(1);
                            }
                        }
                        
                        //$itemQty    = $itemQty > 0 ? $itemQty : 1;
                        if($itemQty > 0) {
                            if (isset($info['custom_price'])) {
                                $itemPrice  = $this->_parseCustomPrice($info['custom_price']);
                            } else {
                                $itemPrice = null;
                            }
                            $noDiscount = !isset($info['use_discount']);
                            
                            if (empty($info['action']) || !empty($info['configured'])) {
                                $item->setQty($itemQty);
                                $item->setCustomPrice($itemPrice);
                                $item->setOriginalCustomPrice($itemPrice);
                                $item->setNoDiscount($noDiscount);
                                $item->getProduct()->setIsSuperMode(true);
                                $item->getProduct()->unsSkipCheckRequiredOption();
                                $item->checkData();
                            } else {
                                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
                            }
                        } else {
                            $this->getQuote()->removeItem($item->getId());
                        }
                    } else {
                        try {
                            $this->addProduct($itemId, $info);
                        }
                        catch (Mage_Core_Exception $e){
                            $this->getSession()->addError($e->getMessage());
                        }
                        catch (Exception $e){
                            return $e;
                        }                        
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->recollectCart();
                throw $e;
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->recollectCart();
        }
        return $this;
    }

    public function updateCustomPrice($data)
    {
        if (is_array($data)) {
            try {
                foreach ($data as $itemId => $info) {
                    if (!empty($info['configured'])) {
                        $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                        $itemQty = (float)$item->getQty();
                    } else {
                        $p = Mage::getModel('catalog/product')->load($itemId);
                        $item       = $this->getQuote()->getItemByProduct($p);
                        
                        $itemQty    = (float)$info['qty'];
                    }
                    if ($item) {
                        if ($item->getProduct()->getStockItem()) {
                            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                                $itemQty = (int)$itemQty;
                            } else {
                                $item->setIsQtyDecimal(1);
                            }
                        }
                        
                        //$itemQty    = $itemQty > 0 ? $itemQty : 1;
                        if($itemQty > 0) {
                            if (isset($info['custom_price'])) {
                                $itemPrice  = $this->_parseCustomPrice(floatval(preg_replace('/[^\d\.]/', '', $info['custom_price'])));
                            } else {
                                $itemPrice = null;
                            }
                            $noDiscount = !isset($info['use_discount']);
                            
                            if (empty($info['action']) || !empty($info['configured'])) {
                                $item->setQty($itemQty);
                                $item->setCustomPrice($itemPrice);
                                $item->setOriginalCustomPrice($itemPrice);
                                $item->setNoDiscount($noDiscount);
                                $item->getProduct()->setIsSuperMode(true);
                                $item->getProduct()->unsSkipCheckRequiredOption();
                                $item->checkData();
                            } else {
                                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
                            }
                        }
                    } 
                }
            } catch (Mage_Core_Exception $e) {
                $this->recollectCart();
                throw $e;
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->recollectCart();
        }
        return $this;
    }    
    
    public function createOrder()
    {
        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();
    
        if (! $quote->getCustomer()->getId() || ! $quote->getCustomer()->isInStore($this->getSession()->getStore())) {
            $quote->getCustomer()->sendNewAccountEmail('registered', '', $quote->getStoreId());
        }
        $service = Mage::getModel('xpos/adminhtml_sales_service_quote', $quote);
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId();
            if (!$originalId) {
                $originalId = $oldOrder->getIncrementId();
            }
            $orderData = array(
                    'original_increment_id'     => $originalId,
                    'relation_parent_id'        => $oldOrder->getId(),
                    'relation_parent_real_id'   => $oldOrder->getIncrementId(),
                    'edit_increment'            => $oldOrder->getEditIncrement()+1,
                    'increment_id'              => $originalId.'-'.($oldOrder->getEditIncrement()+1)
            );
            $quote->setReservedOrderId($orderData['increment_id']);
            $service->setOrderData($orderData);
        }
    
        $order = $service->submit();
        if (!$quote->getCustomer()->getId() || !$quote->getCustomer()->isInStore($this->getSession()->getStore())) {
            $quote->getCustomer()->setCreatedAt($order->getCreatedAt());
            $quote->getCustomer()->save();
        }
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
    
            $this->getSession()->getOrder()->setRelationChildId($order->getId());
            $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
            $this->getSession()->getOrder()->cancel()
            ->save();
            $order->save();
        }
        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }
    
        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));
    
        return $order;
    }    
}
?>
