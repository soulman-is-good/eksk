<?php
/**
 * Description of User_Settings
 *
 * @author Soul_man
 */
class User_Address extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'user_address';
    
    private static $cities = array();
    private static $regions = array();
    
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'city_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('City','id','default'=>'title')),
        'region_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('City_Region','id','default'=>'title')),
        'coord' => array('string[64]','default'=>''),
        'house' => array('string[10]'),
        'flat' => array('string[10]','default'=>''),
        'status' => array('integer[1]','default'=>'1'),
    );
    
    public function onValidate($attr,$pass) {
        /*$pass = false;
        if(($attr == 'mobile' || $attr == 'home' || $attr == 'work') && $this->$attr != '') {
            if(preg_match("/^[0-9]{3} [0-9]{3}.{0,1}[0-9]{2}.{0,1}[0-9]{2}$/",$this->$attr) == false){
                $this->addError($attr,'Не корректно указан номер телефона.');
            }
        }*/
    }

    public function fieldNames() {
        return array(
            'user_id' => X3::translate('Дядя'),
            'city_id' => X3::translate('Город'),
            'region_id' => X3::translate('Улица'),
            'coord' => X3::translate('На карте'),
            'house' => X3::translate('№ дома'),
            'flat' => X3::translate('№ квартиры'),
        );
    }
    
    public function getCity() {
        if(!isset(self::$cities[$this->city_id]))
            self::$cities[$this->city_id] = City::getByPk($this->city_id);
        return self::$cities[$this->city_id];
    }
    
    public function getStreet() {
        if(!isset(self::$regions[$this->region_id]))
            self::$regions[$this->region_id] = City_Region::getByPk($this->region_id);
        return self::$regions[$this->region_id];
    }

    public function moduleTitle() {
        return 'Адреса пользователя';
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
