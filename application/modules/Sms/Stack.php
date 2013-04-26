<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of News
 *
 * @author Soul_man
 */
class Sms_Stack extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'sms_stack';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'phone'=>array('string[255]'),
        'from'=>array('string[255]','default'=>'.'),
        'text'=>array('content'),
        'created_at'=>array('datetime','default'=>'0'),
        'sent_at'=>array('datetime','default'=>'0'),
        'status'=>array('integer[3]','default'=>'-1'),
    );
    public function getStatusLabel(){
        return isset(Sms::$errorCodes[$this->status])?Sms::$errorCodes[$this->status]:'нет статуса';
    }

    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr=array(),$single=false,$class=__CLASS__) {
        return parent::get($arr,$single,$class);
    }
    public static function getByPk($pk,$class=__CLASS__) {
        return parent::getByPk($pk,$class);
    }
    
    public static function add($phone, $text,$from="."){
        $m = new self;
        $user = X3::db()->fetch("SELECT u.id, smsCount FROM user_settings s INNER JOIN data_user u ON u.id=s.user_id WHERE u.phone LIKE '$phone'");
        $ph = "7".substr(preg_replace("/[^0-9]/", "", $phone),0,10);
        $dayb = mktime(0,0,0,(int)date('m'),(int)date('j'),(int)date('Y'));
        $daye = mktime(23,59,59,(int)date('m'),(int)date('j'),(int)date('Y'));
        $sent = X3::db()->fetch("SELECT COUNT(0) `cnt` FROM `sms_stack` WHERE phone='{$ph}' AND status=0 AND sent_at BETWEEN $dayb AND $daye");//sent sms
        if(!User::isUserOnline($user['id']) && $sent['cnt']<=$user['smsCount']){
            $m->phone = $ph;
            $m->text = $text;
            $m->from = $from;
            $m->created_at = time();
            $m->save();
        }
    }
    
    public function fieldNames() {
        return array(
            'from'=>'От кого',
            'phone'=>'Телефон',
            'text'=>'Текст письма',
            'sent_at'=>'Обработано',
            'created_at'=>'Добавлено',
            'status'=>'Отправлено',
        );
    } 
    public function moduleTitle() {
        return 'Стек СМС Рассылки';
    }
    
    public function getDefaultScope() {
        return array(
            '@order'=>'created_at DESC'
        );
    }
}
?>
