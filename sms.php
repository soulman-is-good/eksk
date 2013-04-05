#!/usr/bin/php
<?php
$config = array();
$config = json_decode(file_get_contents("smsconfig.json"), 1);
if (empty($config) || !isset($config['mysql']))
    throw new Exception('Error reading config file. Check existance and json validity, check mysql config part');

include_once __DIR__ . "/smsc_smpp.php";
include_once __DIR__ . "/application/x3framework/X3/X3_MySQLConnection.php";

$table = isset($config['mysql']['table'])?$config['mysql']['table']:"sms_stack";
$attributes = isset($config['mysql']['attributes'])?$config['mysql']['attributes']:array('phone'=>'phone','message'=>'message','sender'=>'sender');
$smpp = new SMPP($config);
$db = new X3_MySQLConnection($config['mysql']);
while (1) {
    if ($smpp == null) {
        $smpp = new SMPP($config);
    }
    $q = $db->query("
        SELECT id,
        `{$attributes['phone']}` AS `phone`,`{$attributes['message']}` AS `message`,`{$attributes['sender']}` AS `sender`  
        FROM `$table`
        WHERE `status`=-1
        ");
    if(!is_resource($q)){
        throw new Exception($db->getErrors());
    }
    $i = 1;
    $db->startTransaction();
    while($model = mysql_fetch_assoc($q)){
        $res = (int)$smpp->send_sms($model['phone'], $model['message'], $model['sender']);
        $time = time();
        $db->addTransaction("UPDATE `$table` SET `status`='$res', `sent_at`='$time' WHERE `id`='{$model['id']}'");
        if($i++%200==0){
            $db->commit();
            sleep(1);
        }
    }
    $db->commit();
    sleep(10);
}