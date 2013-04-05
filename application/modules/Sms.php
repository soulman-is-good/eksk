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
class Sms extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'data_sms';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'name'=>array('string[255]'),
        'title'=>array('string[255]'),
        'text'=>array('content'),
        'status'=>array('boolean','default'=>'1'),
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
    public function fieldNames() {
        return array(
            'name'=>'ID Шаблона',
            'title'=>'Заголовок',
            'text'=>'Текст СМС',
            'status'=>'Отправлять',
        );
    } 
    public function moduleTitle() {
        return 'СМС Шаблоны';
    }
        
}
?>
