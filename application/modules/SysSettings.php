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
class SysSettings extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'sys_settings';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'name'=>array('string[255]','unique','null'),
        'title'=>array('string[255]','null'),
        'value'=>array('text','null','language'),
        'type'=>array('string[128]','default'=>'string[255]'),
        'group'=>array('string[255]','default'=>'Прочие'),
    );
    
    private static $_settings = array();

    public function fieldNames(){
        return array(
            'value'=>'Значение',
        );
    }
    
    public function moduleTitle() {
        return 'Настройки';
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
    public static function getValue($name,$type='string[255]',$title=null,$group=null,$default=null) {
        if(isset(self::$_settings[$name]))
            return self::$_settings[$name];
        $self = new self();
        $s = $self->table->select('*')->where("name='$name'")->asObject(true);
        if($s==null){
            $self->name = $name;
            $self->type = $type;
            $self->title = $title!=null?$title:$name;
            $self->value = $default!=null?$default:'Не задано значение - '.$title;
            $self->group = $group!=null?$group:'Прочие';
            $self->save();
        }else
            $self = $s;
        self::$_settings[$name] = $s->value;
        return $self->value;
    }

    public function actionAdmin(){
        if(X3::app()->user->isGuest())  throw new X3_404();
        $models = $this->table->select('*')->order('`group`')->asObject();
        $user = User::getInstance()->table->select('id')->where("username='".X3::app()->user->login."'")->asObject(true);
        return $this->renderPartial('@views:syssettings:admin.php',array('models'=>$models,'user'=>$user),true);
    }

    public function actionUpdate() {
        if(X3::app()->user->isGuest())  throw new X3_404();
        if(isset($_POST['Syssettings'])){
            $this->attributes = $_POST['Syssettings'];
            $this->_fields['value'][0] = $this->type;
            if($this->type=='file') {
                $h = new Upload($this,'value');
                $k = $h->save();
            }

            if($this->save()){
                exit;
            }
            elseif(empty($this->_errors)) throw new X3_Exception(X3::app()->db->getErrors(),X3::DB_ERROR);
            else{
            $errors = '<ul>';
            foreach($this->_errors as $err)
                    foreach($err as $er)
                        $errors .= '<li>'.$er.'</li>';
            $errors .= '</ul>';
            echo $errors;
            }exit;
        }else{
            $id = (int)$_GET['id'];
            if($id>0)
                $this->select('*')->where("id=".$id)->asObject(true);
            else
                 echo "NO SUCH SysSettings $id";
        }

        $this->renderPartial("update");
    }
    
    public function getDefaultScope() {
        if(isset($_GET['group'])){
            $gr = urldecode($_GET['group']);
            return array('@condition'=>array('group'=>$gr),'@order'=>'title');
        }
        return array();
    }
    
}
?>
