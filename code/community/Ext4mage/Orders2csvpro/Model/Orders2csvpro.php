<?php
/**
 * Ext4mage Orders2csvpro Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to Henrik Kier <info@ext4mage.com> so we can send you a copy immediately.
 *
 * @category   Ext4mage
 * @package    Ext4mage_Orders2csvpro
 * @copyright  Copyright (c) 2012 Ext4mage (http://ext4mage.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Henrik Kier <info@ext4mage.com>
 * */
class Ext4mage_Orders2csvpro_Model_Orders2csvpro extends Mage_Core_Model_Abstract
{
    const XPATH_CONFIG_SETTINGS_IS_ACTIVE		= 'orders2csvpro/settings/is_active';
	const XPATH_CONFIG_SETTINGS_FILE_ID        	= 'orders2csvpro/settings/file';
	const ENCLOSURE = '"';
    const DELIMITER = ',';


    /**
    * Main function being called in order grid
    *
    * @param $orders Orders (Mage_Sales_Model_Order) to be saved in file.
    * @return String filename
    */
    public function saveOrdersAsCsv($orders){
    	$fileStructur = Mage::getModel('orders2csvpro/file')->load(Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_FILE_ID));
    	$fileName = str_replace(' ', '_', $fileStructur->getTitle());
    	$fileName .= '_'.date("Ymd_His").'.csv';
    	$fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');
    
    	$this->writeTopRow($fileStructur, $fp);
    	foreach ($orders as $order) {
    		$order = Mage::getModel('sales/order')->load($order);
    		$this->writeLines($order, $fileStructur, $fp);
    	}
    
    	fclose($fp);
    	return $fileName;
    }
    
    /**
     * Function being called to generate csv in cron job
     *
     * @param $orders Orders (Mage_Sales_Model_Order) to be saved in file.
     * @param $fileStructur File (Ext4mage_Orders2csvpro_Model_File) to be used.
     * @param $schedule Schedule (Ext4mage_Orders2csvpro_Model_Schedule) to be used.
     * @param $test Boolean
     * @return String filename
     */
    public function generateOrdersAsCsv($orders, $fileStructur, $schedule, $test = false){
    	$cvsText = "";
    	
    	if($schedule->getShowHeader()==1)
        	$cvsText .= $this->writeTopRow($fileStructur);
        
        foreach ($orders as $order) {
        	$order = Mage::getModel('sales/order')->load($order);
            $cvsText .= $this->writeLines($order, $fileStructur);
            
            if($test === false) {
            	//put in the orders that has been processed in orders2cvs_runs
            	$run = Mage::getModel('orders2csvpro/runs');
            	$run->setOrderId($order->getId());
            	$run->setScheduleId($schedule->getId());
            	$run->save();
            }
        }
		
        return $cvsText;
    }
    
    //sending the csv file as attachment in email
    public function sendEmailAction($schedule, $cvsText, $fileStructur, $fileName = null) {
    	
    	if($fileName == null){
    		$fileName = str_replace(' ', '_', $fileStructur->getTitle());
    		$fileName .= '_'.date("Ymd_His").'.csv';
    	}
    	
    	$mailTemplate = Mage::getModel('core/email_template');
    	$mailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
    	$mailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
    	$mailTemplate->setTemplateSubject($schedule->getTitle());
    	
    	if($schedule->getAttached() == 1){
	    	$mailTemplate->getMail()->createAttachment(
		    	$cvsText,
		    	Zend_Mime::TYPE_OCTETSTREAM,
		    	Zend_Mime::DISPOSITION_ATTACHMENT,
		    	Zend_Mime::ENCODING_BASE64,
		    	$fileName
	    	);
    	}else{
    		$mailTemplate->setTemplateText($cvsText);
    	}
    	
    	$mailTemplate->send($schedule->getEmail());
    }
    
    /**
    * Cron function being called
    *
    */
    public function cronRun($test = false, $scheduleId = null){
    	if(Mage::getStoreConfig(self::XPATH_CONFIG_SETTINGS_IS_ACTIVE) == 0 && $scheduleId == null){
    		return;
    	}
    	
    	if($scheduleId === null){
    		$schedules = Mage::getModel('orders2csvpro/schedule')->getActiveSchedules();
    		$test = false;
    	}else{
    		$schedules[] = Mage::getModel('orders2csvpro/schedule')->load($scheduleId);
    	}
    	
    	foreach($schedules as $schedule){
    		$doRun = false;
    		if($schedule->getPeriode() == 1 || $test){
    			//Send hourly
    			$doRun = true;
    		}elseif(date("G")==0 && $schedule->getPeriode() == 2){
    			//Send dayli
    			$doRun = true;    			 
    		}elseif(date('w') == 0 && date("G")==0 && $schedule->getPeriode() == 3){
    			//Send weekly
    			$doRun = true;    			 
    		}elseif(date('j') == 1 && date("G")==0 && $schedule->getPeriode() == 4){
    			//Send monthly
    			$doRun = true;    			 
    		}elseif((date('n') == 1 || date('n') == 4 || date('n') == 7 || date('n') == 10) && date('j') == 1 && date("G")==0 && $schedule->getPeriode() == 5){
    			//Send Quarterly 
    			$doRun = true;    			 
    		}elseif((date('n') == 1 || date('n') == 7 ) && date('j') == 1 && date("G")==0 && $schedule->getPeriode() == 6){
    			//Send Semi-annually 
    			$doRun = true;    			 
    		}elseif(date('z') == 0 && date("G")==0 && $schedule->getPeriode() == 7){
    			//Send Annually 
    			$doRun = true;    			 
    		}
    		
    		if($doRun){
    			$orders =  Mage::getModel('orders2csvpro/runs')->getAllOrdersNotRun($schedule);
    			if(count($orders)>0){
	    			$fileStructur = Mage::getModel('orders2csvpro/file')->load($schedule->getFileId());
	    			$cvsText = $this->generateOrdersAsCsv($orders, $fileStructur, $schedule, $test);
	    			$this->sendEmailAction($schedule, $cvsText, $fileStructur);
	    			$schedule->setLastRun(Mage::getSingleton('core/date')->gmtDate());
	    			$schedule->save();
	    			
	    			Mage::log("Orders2CSV PRO - Schedule ".$schedule->getTitle()." has run with ".count($orders)." orders", Zend_Log::INFO);
    			}else{
    				//Mage::log("Orders2CSV PRO - Schedule ".$schedule->getTitle()." has NOT run - No new orders", Zend_Log::INFO);
    			}
    		}
    	}
    }

    /**
	 * Writes top row with the names provided in columns title.
	 * 
	 * @param $fp The cvs file
	 * @param $fileStructur The filestructur set in settings
	 */
    protected function writeTopRow($fileStructur, $fp = null)
    {
    	$columns = $fileStructur->getColumns();
    	 
    	$headerTitles = null;
    	foreach ($columns as $column){
    		$headerTitles[] = $column->getTitle();
    	}
    	
    	if($fp == null){
    		return $this->generateCsv($headerTitles, self::DELIMITER, self::ENCLOSURE);
    	}else{
    		fputcsv($fp, $headerTitles, self::DELIMITER, self::ENCLOSURE);
    	}
    	
    }

    /**
	 * Make the single order lines
	 * 
	 * @param Mage_Sales_Model_Order $order The order to write csv of
	 * @param $fp The file handle of the csv file
	 */
    protected function writeLines($order, $fileStructur, $fp = null) 
    {
        $common = $this->getCommonOrderValues($order);
		$csvTextLinies = "";
		
        $columns = $fileStructur->getColumns();
        $rows = null;
        $values = null;
        $runItems = false;
        foreach ($columns as $column){
        	$value = "";
        	$matches = null;
        	
        	if(preg_match('/order_data_(.*)/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_shipping_data_(.*)/',$column->getValue(),$matches)){
        		if(is_object($order->getShippingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getShippingAddress()->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_shipping_country_name/',$column->getValue(),$matches)){
        		if(is_object($order->getShippingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getShippingAddress()->getCountryModel()->getName(), $column->getValue());
        	}elseif(preg_match('/order_billing_data_(.*)/',$column->getValue(),$matches)){
        		if(is_object($order->getBillingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getBillingAddress()->getData($matches[1]), $column->getValue());
        	}elseif(preg_match('/order_billing_country_name/',$column->getValue(),$matches)){
        		if(is_object($order->getBillingAddress()))
        			$value = preg_replace('/'.$matches[0].'/', $order->getBillingAddress()->getCountryModel()->getName(), $column->getValue());
        	}elseif(preg_match('/order_shipping_description/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getShippingDescription(), $column->getValue());
        	}elseif(preg_match('/order_payment_block/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', preg_replace("{{{pdf_row_separator}}}", " : ",Mage::helper("payment")->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf()), $column->getValue());
        	}elseif(preg_match('/order_store_url/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getStore()->getUrl(), $column->getValue());
        	}elseif(preg_match('/order_store_base_url/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->getStore()->getBaseUrl(), $column->getValue());
        	}elseif(preg_match('/order_num_invoices/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasInvoices(), $column->getValue());
        	}elseif(preg_match('/order_num_shipments/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasShipments(), $column->getValue());
        	}elseif(preg_match('/order_num_creditmemos/',$column->getValue(),$matches)){
        		$value = preg_replace('/'.$matches[0].'/', $order->hasCreditmemos(), $column->getValue());
        	}elseif(preg_match('/order_customer_group/',$column->getValue(),$matches)){
			   if(is_object($order->getCustomerGroupId()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getCustomerGroupId()->getCode(), $column->getValue());
			}elseif(preg_match('/order_base_currency_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getBaseCurrency()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getBaseCurrency()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_base_total_due/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getBaseTotalDue(), $column->getValue());
			}elseif(preg_match('/order_created_full/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getCreatedAtFormated("full"), $column->getValue());
			}elseif(preg_match('/order_created_long/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getCreatedAtFormated("long"), $column->getValue());
			}elseif(preg_match('/order_created_medium/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getCreatedAtFormated("medium"), $column->getValue());
			}elseif(preg_match('/order_created_short/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getCreatedAtFormated("short"), $column->getValue());
			}elseif(preg_match('/order_email_customer_note/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getEmailCustomerNote(), $column->getValue());
			}elseif(preg_match('/order_is_not_virtual/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getIsNotVirtual(), $column->getValue());
			}elseif(preg_match('/order_currency_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getOrderCurrency()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getOrderCurrency()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_payment_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getPayment()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getPayment()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_payment_auth_trans_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getPayment()->getAuthorizationTransaction()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getPayment()->getAuthorizationTransaction()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_real_id/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getRealOrderId(), $column->getValue());
			}elseif(preg_match('/order_shipping_carrier_code/',$column->getValue(),$matches)){
			   if(is_object($order->getShippingCarrier()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getShippingCarrier()->getCarrierCode(), $column->getValue());
			}elseif(preg_match('/order_shipping_carrier_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getShippingCarrier()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getShippingCarrier()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_status_label/',$column->getValue(),$matches)){
			   $value = preg_replace('/'.$matches[0].'/', $order->getStatusLabel(), $column->getValue());
			}elseif(preg_match('/order_store_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getStore()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getStore()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/order_store_group_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object($order->getStore()->getGroup()))
			      $value = preg_replace('/'.$matches[0].'/', $order->getStore()->getGroup()->getData($matches[1]), $column->getValue());
			}elseif(preg_match('/customer_data_(.*)/',$column->getValue(),$matches)){
			   if(is_object(Mage::getModel('customer/customer')->load($order->getCustomerId())))
			      $value = preg_replace('/'.$matches[0].'/', Mage::getModel('customer/customer')->load($order->getCustomerId())->getData($matches[1]), $column->getValue());
			}
			
        	if(in_array($matches[0], Mage::helper('orders2csvpro')->getCurrencyKeys())){
        		switch($fileStructur->getNumFormatting()){
        			case 2:
        				$value = $order->getStore()->convertPrice($value);
        				break;
        			case 3:
        				$value = $order->formatPriceTxt($value);
        				break;
        		}
        	}
        	
        	if(preg_match('/item_(.*)/',$column->getValue())){
        		$runItems = true;
        	}
        	$values[$column->getValue()] = $value;
        }

        $orderItems = $order->getItemsCollection();
        if($runItems){
	        foreach ($orderItems as $item){
	            if (!$item->isDummy()) {
	            	foreach ($columns as $column){
	            		$value = "";
	            		$matches = null;
	            		
	            		if(preg_match('/item_data_(.*)/',$column->getValue(),$matches)){
	        				$value = preg_replace('/'.$matches[0].'/',$item->getData($matches[1]), $column->getValue());
	            		}elseif(preg_match('/item_status/',$column->getValue(),$matches)){
	        				$value = preg_replace('/'.$matches[0].'/',$item->getStatus(), $column->getValue());
	            		}elseif(preg_match('/product_data_(.*)/',$column->getValue(),$matches)){
						   if(is_object(Mage::getModel('catalog/product')->load($item->getData('product_id'))))
						   	  $value = preg_replace('/'.$matches[0].'/', Mage::getModel('catalog/product')->load($item->getData('product_id'))->getData($matches[1]), $column->getValue());
						}
	            		
	            		if(in_array($matches[0], Mage::helper('orders2csvpro')->getCurrencyKeys())){
	            			switch($fileStructur->getNumFormatting()){
	            				case 2:
	            					$value = $order->getStore()->convertPrice($value);
	            					break;
	            				case 3:
	            					$value = $order->formatPriceTxt($value);
	            					break;
	            			}
	            		}
	            		if($value != null)
	            			$values[$column->getValue()] = $value;
	            	}
	            	 
	            	$options = $this->getItemOptions($item);
	            	foreach ($options as $option){
		            	foreach ($columns as $column){
		            		$value = "";
		            		$matches = null;
		            		if(preg_match('/item_option_data_(.*)/',$column->getValue(),$matches)){
		            			$value = preg_replace('/'.$matches[0].'/', $option[$matches[1]], $column->getValue());
		            		}
		            		if($value != null)
		            			$values[$column->getValue()] = $value;
		            	}
		            	$rows[] = $values;            		
	            	}
	            	if(count($options)==0)
	            		$rows[] = $values;
	            }
	        }
	        
	        foreach ($rows as $row)
	        	if($fp == null){
	        		$csvTextLinies .= $this->generateCsv($row, self::DELIMITER, self::ENCLOSURE);
	        	}else{
	        		fputcsv($fp, $row, self::DELIMITER, self::ENCLOSURE);
	        	}
	        	
        }else{
        	if($fp == null){
	        	$csvTextLinies .= $this->generateCsv($values, self::DELIMITER, self::ENCLOSURE);
	        }else{
	        	fputcsv($fp, $values, self::DELIMITER, self::ENCLOSURE);
	        }
        }
        return $csvTextLinies;
    }

	/**
	 * Get all option values from a general item
	 *
	 * @param item $item
	 * @return array of options
	 */
	public function getItemOptions($item) {
		$result = array();
		if(method_exists($item, 'getOrderItem')){
			$orderItem = $item->getOrderItem();
		}else{
			$orderItem = $item;
		}
		if ($options = $orderItem->getProductOptions()) {
			if (isset($options['options'])) {
				$result = array_merge($result, $options['options']);
			}
			if (isset($options['additional_options'])) {
				$result = array_merge($result, $options['additional_options']);
			}
			if (isset($options['attributes_info'])) {
				$result = array_merge($result, $options['attributes_info']);
			}
		}
		return $result;
	}
	
	/**
	 * Make variables as cvs
	 *
	 * @return csv string
	 */
	function generateCsv($fields, $delimiter = ',', $enclosure = '"', $escape = '\\')
	{
		$first = false;
		$linie = "";
		foreach ($fields as $field) {
			if ($first) $linie .= ",";
	
			$f = str_replace($enclosure, $enclosure.$enclosure, $field);
			if ($enclosure != $escape) {
				$f = str_replace($escape.$enclosure, $escape, $f);
			}
			if (strpbrk($f, " \t\n\r".$delimiter.$enclosure.$escape) || strchr($f, "\000")) {
				$linie .=  $enclosure.$f.$enclosure;
			} else {
				$linie .= $f;
			}
	
			$first = true;
		}
		$linie .= "\n";
		return $linie;
	}
}
?>