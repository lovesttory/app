<?php

class Wyomind_Datafeedmanager_Model_Observer
{	
	

    

    /**
     * Cronjob expression configuration
     */
   

   public function scheduledGenerateFeeds($schedule)
    {
    	
    	
    	
        $errors = array();

      
        $collection = Mage::getModel('datafeedmanager/datafeedmanager')->getCollection();
        /* @var $collection Mage_googleshopping_Model_Mysql4_googleshopping_Collection */
      
        foreach ($collection as $datafeed) {
           
            
		
            try {
                $cron=(Mage::getModel('cron/schedule')->setCronExpr($datafeed->getCronExpr())->trySchedule(time()));   
                //echo "id :: ".$datafeed->getFeedId()." :: executed ::".$cron."<br>";
                if($cron)$datafeed->generateFile();
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        } 
    }
}