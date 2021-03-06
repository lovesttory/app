<?php

class SM_Core_Helper_Data extends Mage_Core_Helper_Abstract {

    public function checkLicense($product, $key, $update = false) {
        return true;
//        if ($update)
//            $this->checkUpdate();
# Get Variables from storage (retrieve from wherever it's stored - DB, file, etc...)
        $session = Mage::getSingleton('adminhtml/session');
        $msgs = $session->getMessages(true);
                $msgs->deleteMessageByIdentifier($product);
        
        $licensekey = $key;
        $dir = Mage::getBaseDir("var") . DS . "smartosc" . DS . strtolower(substr($product, 0, 5)) . DS;
        $filepath = $dir . "license.dat";
        $file = new Varien_Io_File;
        if (!$localkey = $file->read($filepath))
            $localkey = "";

# The call below actually performs the license check. You need to pass in the license key and the local key data
        if (!$update)
            $results = $this->_checkLicense($licensekey, $localkey);
        else
            $results = $this->_checkLicense($licensekey);
# For Debugging, Echo Results
//        ob_start();
//        echo "<textarea cols=100 rows=20>";
//        print_r($results);
//        echo "</textarea>";
//die;
        if (strtoupper($results["status"]) == "ACTIVE") {
            # Allow Script to Run
            if (strtoupper($results['productname']) == strtoupper($product)) {
                if ($results["localkey"]) {
                    # Save Updated Local Key to DB or File
                    $localkeydata = $results["localkey"];
                    if (!is_dir_writeable($dir)) {
                        $file->checkAndCreateFolder($dir);
                    }
                    if (!$file->write($filepath, $localkeydata))
                        die('Cannot update licensing data to ' . $filepath);
                }
                Mage::getModel('core/config')->saveConfig($product . '/general/license_status', $results["status"] . " until " . $results['nextduedate']);
                Mage::getConfig()->cleanCache();
                if ($update) {
                    $session->addSuccess("The license key is valid!");
                    if ($msgs->getLastAddedMessage())
                        $msgs->getLastAddedMessage()->setIdentifier($product);
                }

                return true;
            }
        } elseif ($results["status"] == "Invalid") {
            $message = $results["status"];
        } elseif ($results["status"] == "Expired") {
            $message = $results["status"];
        } elseif ($results["status"] == "Suspended") {
            $message = $results["status"];
        }
        $session->addError('The "' . $product . '" extension has been disabled or your license key is invalid!');
        if ($msgs->getLastAddedMessage())
            $msgs->getLastAddedMessage()->setIdentifier($product);
        Mage::getModel('core/config')->saveConfig($product . '/general/license_status', $message);
        Mage::getConfig()->cleanCache();
        return false;
    }

    protected function _checkLicense($licensekey, $localkey = "") {
        $whmcsurl = "http://bill.smartosc.com/";
        $licensing_secret_key = "23langha"; # Set to unique value of chars
        $checkdate = date("Ymd"); # Current date
        $localkeydays = 3000; # How long the local key is valid for in between remote checks
        $allowcheckfaildays = 0; # How many days to allow after local key expiry before blocking access if connection cannot be made
        $localkeyvalid = false;
        if ($localkey) {
            $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
            $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
            $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
            if ($md5hash == md5($localdata . $licensing_secret_key)) {
                $localdata = strrev($localdata); # Reverse the string
                $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
                $localdata = substr($localdata, 32); # Extract License Data
                $localdata = base64_decode($localdata);
                $localkeyresults = unserialize($localdata);
                $originalcheckdate = $localkeyresults["checkdate"];
                if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                    $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                    if ($originalcheckdate > $localexpiry) {
                        $localkeyvalid = true;
                        $results = $localkeyresults;
                        $validdomains = explode(",", $results["validdomain"]);
                        if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                            $localkeyvalid = false;
                            $localkeyresults["status"] = "Invalid";
                            $results = array();
                        }
                        $validips = explode(",", $results["validip"]);
                        if (!in_array($_SERVER['SERVER_ADDR'], $validips)) {
                            $localkeyvalid = false;
                            $localkeyresults["status"] = "Invalid";
                            $results = array();
                        }
                        // if ($results["validdirectory"] != dirname(__FILE__)) {
                        //     $localkeyvalid = false;
                        //     $localkeyresults["status"] = "Invalid";
                        //     $results = array();
                        // }
                    }
                }
            }
        }
        if (!$localkeyvalid) {
            $postfields["licensekey"] = $licensekey;
            $postfields["domain"] = $_SERVER['SERVER_NAME'];
            $postfields["ip"] = $_SERVER['SERVER_ADDR'];
            $postfields["dir"] = dirname(__FILE__);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl . "modules/servers/licensing/verify.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            if (!$data) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
                if ($originalcheckdate > $localexpiry) {
                    $results = $localkeyresults;
                } else {
                    $results["status"] = "Remote Check Failed";
                    return $results;
                }
            } else {
                preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
                $results = array();
                foreach ($matches[1] AS $k => $v) {
                    $results[$v] = $matches[2][$k];
                }
            }
            if ($results["status"] == "Active") {
                $results["checkdate"] = $checkdate;
                $data_encoded = serialize($results);
                $data_encoded = base64_encode($data_encoded);
                $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
                $data_encoded = strrev($data_encoded);
                $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
                $data_encoded = wordwrap($data_encoded, 80, "\n", true);
                $results["localkey"] = $data_encoded;
            }
            $results["remotecheck"] = true;
        }
        unset($postfields, $data, $matches, $whmcsurl, $licensing_secret_key, $checkdate, $localkeydays, $allowcheckfaildays, $md5hash);
        return $results;
    }

    public function checkUpdate() {
        $newNotification = array(
            array(
                'severity' => 4,
                'date_added' => gmdate('Y-m-d H:i:s'),
                'title' => "test",
                'description' => "hehe",
                'url' => (string) "http://www.smartosc.com/news/id/1234",
            )
        );

        Mage::getModel('adminnotification/inbox')->parse($newNotification);
    }

}

?>
