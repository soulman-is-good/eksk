#!/usr/bin/php
<?php
define('ROOT','/var/www');
$config = array('host'=>'89.111.21.7','port'=>'2345','login'=>'eokz','password'=>'62d96e');
$config = json_decode(file_get_contents(ROOT."/smsconfig.json"), 1);
if (empty($config) || !isset($config['mysql']))
    throw new Exception('Error reading config file. Check existance and json validity, check mysql config part');

require_once ROOT . '/application/extensions/php-smpp/smppclient.class.php';
require_once ROOT . '/application/extensions/php-smpp/gsmencoder.class.php';
require_once ROOT . '/application/extensions/php-smpp/sockettransport.class.php';
include_once ROOT . "/application/x3framework/X3.php";
include_once ROOT . "/application/x3framework/X3/X3_Component.php";
include_once ROOT . "/application/x3framework/X3/X3_MySQLConnection.php";

$table = isset($config['mysql']['table'])?$config['mysql']['table']:"sms_stack";
$attributes = isset($config['mysql']['attributes'])?$config['mysql']['attributes']:array('phone'=>'phone','message'=>'message','sender'=>'sender');
////New database connection
$db = new X3_MySQLConnection($config['mysql']);
date_default_timezone_set("Asia/Almaty");
////Establish socket smpp connection
//$smpp = new SMPP($config);
$transport = new SocketTransport(array($config['host']),array($config['port']));
$transport->setSendTimeout(100);
$transport->setRecvTimeout(10000);
SmppClient::$system_type = "SMPP";
$smpp = new SmppClient($transport);
//Allow debug dump output
$smpp->debug = true;
$transport->debug = true;


$from = new SmppAddress('eksk', SMPP::TON_ALPHANUMERIC);
while (1) {
    $q = $db->query("
        SELECT id,
        `{$attributes['phone']}` AS `phone`,`{$attributes['message']}` AS `message`,`{$attributes['sender']}` AS `sender`  
        FROM `$table`
        WHERE `status`<>0
        ");
    if(!is_resource($q)){
        $smpp->close();
        throw new Exception($db->getErrors());
    }
    $i = 1;
    $db->startTransaction();
    while($model = mysql_fetch_assoc($q)){
        if (!$transport->isOpen()) {
            $transport->open();
            $smpp->bindTransmitter($config['login'],$config['password']);
        }
        $res = -1;
        try{
            $message = $model['message'];
            $phone = implode(' ',array(substr($model['phone'], 1,3),substr($model['phone'], 4,3),substr($model['phone'], 7,2),substr($model['phone'], 9,2)));
            $user = $db->fetch("SELECT smsTime FROM user_settings s INNER JOIN data_user u ON u.id=s.user_id WHERE u.phone LIKE '$phone'");
            if($user){
                $time = explode('-',$user['smsTime']);
                $f = explode(':',$time[0]);$f = (int)$f[0] * 60 + (int)$f[1];
                $t = explode(':',$time[1]);$t = (int)$t[0] * 60 + (int)$t[1];
                $curtime = (int)date("H") * 60 + (int)date("i");
                $now = date("H:i");
                echo "===========================\r\ntime:$time[0]-$time[1],now:$now\r\n$f < $curtime < $t\r\n===================================\r\n\r\n";
                if(($f < $t && $curtime > $f && $curtime <= $t) || ($f >= $t && ( ($f<=$curtime && $curtime<1440) || (0<=$curtime && $curtime<=$t) ))) {
                   $res = 0;
                    $encodedMessage = iconv('UTF-8', "UTF-16BE", $message);//GsmEncoder::utf8_to_gsm0338($message);
                    $to = new SmppAddress($model['phone'], SMPP::TON_INTERNATIONAL,SMPP::NPI_E164);
                    $smpp->sendSMS($from, $to, $encodedMessage,null,SMPP::DATA_CODING_UCS2);
                }
            }else{
                $db->query("DELETE FROM sms_stack WHERE id='{$model['id']}'");
            }
        }catch(SmppException $e){
            $res = (int)$e->getCode();
            $message = $e->getMessage(). "\r\n\t" .$e->getFile(). " (".$e->getLine().")";
            echo "\r\n\t------EXCEPTION $res------\r\n\t$message\r\n\t--------------------\r\n\r\n";
        }
        $time = time();
        $db->addTransaction("UPDATE `$table` SET `status`='$res', `sent_at`='$time' WHERE `id`='{$model['id']}'");
        if($i++%200==0){
            $db->commit();
            sleep(1);
        }
    }
    if($transport->isOpen()){
        $smpp->close();
    }
    $db->commit();
    sleep(10);
}