<?php
class SM_XPos_Helper_Data extends Mage_Core_Helper_Abstract {
    public function __construct() {
      //Mage::helper('smcore')->checkLicense('xpos', Mage::getStoreConfig('xpos/general/key'));
    }    
    public function isEnableModule() {
        return Mage::getStoreConfig('xpos/general/enabled');
    }
    
    public function aboveVersion($version)
    {
        $info = Mage::getVersionInfo();
        
        //Enterprise 1.10 is equivalent to Community 1.4
        if($info['major'] == 1 && $info['minor'] == 10) {
            $info['minor'] = 4;
        }
        
        $version = explode('.', $version);
        return intval($info['major']) >= intval($version[0]) && intval($info['minor']) >= intval($version[1]); 
    }
}

