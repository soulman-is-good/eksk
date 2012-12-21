<?php
/**
 * Description of News
 *
 * @author Soul_man
 */
class Lang extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'sys_languages';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),        
        'value'=>array('text','null','language'),
        'group'=>array('string[255]','default'=>'Прочие')
    );

    public function fieldNames(){
        return array(
            'value'=>'Значение',
        );
    }

    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr,$single=false,$class=__CLASS__) {
        return parent::get($arr,$single,$class);
    }
    public static function getByPk($pk,$class=__CLASS__) {
        return parent::getByPk($pk,$class);
    }

    public static function getValue($value=null,$lang = 'ru') {
        $self = new self();
        $attr = "value";
        if($lang!='ru') $attr = "value_$lang";
        $s = $self->table->select("$attr")->where("value='$value'")->asObject(true);
        if($s==null){
            $self->value = $value;
            foreach(X3::app()->languages as $l){
                $a = "value_$l";
                $self->$a = $value;
            }
            $self->save();
        }else
            $self = $s;
        return $self->$attr;
    }

    public function actionSave() {
        if(X3::app()->user->isGuest())  exit;
        if(isset($_POST['lang']) && isset($_POST['id']) && $_POST['id']>0){
            $id = (int)$_POST['id'];
            $attr = "value_{$_POST['lang']}";
            if(!isset($this->_fields[$attr])) exit;
            $model = self::getByPk($id);
            if(is_null($model)) exit;
            $model->$attr = $_POST['value'];
            $model->save();
        }
        exit;
    }
}