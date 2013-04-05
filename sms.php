#!/usr/bin/php
<?php
$config = array();
$config = json_decode(file_get_contents("smsconfig.json"),1);
if(empty($config))
    throw new Exception ('Error reading config file. Check existance and json validity');

include_once "smsc_smpp.php";

$smpp = new SMPP($config);
echo $smpp->send_sms("7772542975", "Test message");
exit;
while(1){
    if($smpp == null){
        $smpp = new SMPP($config);
    }
    sleep(10);
}