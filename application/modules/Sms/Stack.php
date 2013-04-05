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
        'crated_at'=>array('datetime','default'=>'0'),
        'sent_at'=>array('datetime','default'=>'0'),
        'status'=>array('integer[3]','default'=>'-1'),
    );
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
        $ph = "7".substr(preg_replace("/[^0-9]/", "", $phone),0,10);
        $m->phone = $ph;
        $m->text = $text;
        $m->from = $from;
        $m->created_at = time();
        $m->save();
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
