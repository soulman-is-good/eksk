<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class Vote_Stat extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'vote_stat';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'vote_id'=>array('integer[10]','unsigned','index','ref'=>array('Warning','id','default'=>"title")),
        'address_id'=>array('integer[10]','unsigned','index','ref'=>array('User_Address','id','default'=>"id")),
        'answer'=>array('integer[2]','default'=>'0'),
        'created_at'=>array('datetime')
    );

    public function fieldNames() {
        return array(
            'vote_id'=>'Опрос',
            'address_id'=>'Пользователь',
            'answer'=>'Ответ',
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
