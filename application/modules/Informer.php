<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Informer
 *
 * @author Soul_man
 */
class Informer extends X3_Module_Table {
    public $encoding = 'UTF-8';
    public $tableName = 'data_informer';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'category'=>array('string[255]','default'=>'all'),
        'data'=>array('string[255]','default'=>'[]'),
        'message'=>array('html'),
        'weight'=>array('integer[10]','unsigned','default'=>'0'),
        'status'=>array('boolean','default'=>'0'),
        'created_at'=>array('integer[10]','default'=>'0')
    );
    
    public function _getData() {
        return json_decode($this->getAttribute('data'));
    }
    
    public function _setData($value) {
        if(is_array($value)){
            $this->getAttribute('data',  json_encode($value));
        }
    }
    
    public function actionClearall() {
        $cat = $_GET['cat'];
        Informer::delete(array('category'=>$cat));
        exit;
    }
    
    public static function add($message,$category='all',$data=array()) {
        $I = new self();
        $I->message = $message;
        $I->category = $category;
        $I->data = $data;
        $I->status = 0;
        if(!$I->save()){
            X3::log("Ошибка при сохрании информера '$message'",'kansha_error');
        }
        return $I;
    }
    
    public function beforeSave() {
        if($this->table->getIsNewRecord()){
            $this->created_at = time();
        }
        return parent::beforeSave();
    }
}

?>
