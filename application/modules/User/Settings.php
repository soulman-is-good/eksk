<?php
/**
 * Description of User_Settings
 *
 * @author Soul_man
 */
class User_Settings extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'user_settings';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'about' => array('content[512]','default'=>'NULL'),
        'mobile' => array('string','default'=>''),
        'home' => array('string','default'=>''),
        'work' => array('string','default'=>''),
        'skype' => array('string','default'=>''),
        'email' => array('email','default'=>''),
        'site' => array('string','default'=>''),
    );
    
    public function onValidate($attr,$pass) {
        $pass = false;
        if(($attr == 'mobile') && trim($this->$attr) != '') {
            $this->$attr = str_replace("_", "", $this->$attr);
            if(preg_match("/^[0-9]{3} [0-9]{3}.{0,1}[0-9]{2}.{0,1}[0-9]{2}$/",$this->$attr) == false){
                $this->addError($attr,X3::translate('Не корректно указан номер телефона.'));
            }
        }
        if(($attr == 'home') && trim($this->$attr) != '') {
            $this->$attr = str_replace("_", "", $this->$attr);
            if(preg_match("/^[0-9]{3,5} [0-9\s]{8,9}$/",$this->$attr) == false){
                $this->addError($attr,X3::translate('Не корректно указан номер телефона.'));
            }
        }
        if(($attr == 'work') && trim($this->$attr) != '') {
            $this->$attr = str_replace("_", "", $this->$attr);
            if(preg_match("/^[0-9]{3,5} [0-9\s]{8,9} [0-9]{0,6}$/",$this->$attr) == false){
                $this->addError($attr,X3::translate('Не корректно указан номер телефона.'));
            }
        }
        if($attr == 'site' && $this->$attr!=''){
            $this->$attr = trim(str_replace('javascript:','',$this->$attr));
            if(strpos($this->$attr,'http://')!==0) {
                $this->addError($attr,X3::translate('Не корректно указан Веб-cайт'));
            }
        }
    }

    public function fieldNames() {
        return array(
            'about' => X3::translate('О себе'),
            'mobile' => X3::translate('Мобильный'),
            'home' => X3::translate('Домашний'),
            'work' => X3::translate('Рабочий'),
            'skype' => 'Skype',
            'email' => 'E-Mail',
            'site' => X3::translate('Веб-сайт'),
        );
    }

    public function moduleTitle() {
        return 'Профиль пользователя';
    }

    public function cache() {
        return array(
            //'cache' => array('actions' => 'show', 'role' => '*', 'expire' => '+1 month'),
        );
    }

    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }

    public static function get($arr=array(), $single = false, $class = __CLASS__,$asArray=false) {
        return parent::get($arr, $single, $class,$asArray);
    }

    public static function getByPk($pk, $class = __CLASS__,$asArray=false) {
        return parent::getByPk($pk, $class,$asArray);
    }
    
    public function beforeValidate() {

    }

}

?>
