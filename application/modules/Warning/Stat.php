<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class Warning_Stat extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'warning_stat';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'warning_id'=>array('integer[10]','unsigned','index','ref'=>array('Warning','id','default'=>"title")),
        'user_id'=>array('integer[10]','unsigned','index','ref'=>array('User','id','default'=>"name")),
        'created_at'=>array('datetime')
    );

    public function fieldNames() {
        return array(
            'user_id'=>'Пользователь',
            'warning_id'=>'Оповещение',
        );
    }

    public function beforeValidate() {
        if(strpos($this->created_at,'.')!==false){
            $this->created_at = strtotime($this->created_at);
        }elseif($this->created_at == 0)
            $this->created_at = time();
    }
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
        }
        parent::onDelete($tables, $condition);
    }
}
?>
