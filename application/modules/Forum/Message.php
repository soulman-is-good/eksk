<?php

/**
 * Description of Default
 *
 * @author Soul_man
 */
class Forum_Message extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'forum_message';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'forum_id' => array('integer[10]', 'unsigned', 'index', 'ref' => array('Forum', 'id', 'default' => 'title')),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref' => array('User', 'id', 'default' => 'email')),
        'user_to' => array('integer[10]', 'unsigned','default'=>'NULL', 'index', 'ref' => array('User', 'id', 'default' => 'email')),
        'content' => array('content', 'default'=>'NULL'),
        'status' => array('boolean', 'default' => '0'),
        'created_at' => array('datetime', 'default' => '0'),
    );

    public function fieldNames() {
        return array(
            'created_at' => 'Дата отправки',
            'forum_id' => X3::translate('Тема'),
            'user_to' => X3::translate('Кому'),
            'content' => X3::translate('Сообщение'),
            'status' => 'Прочитанное',
        );
    }

    public static function getUserList() {
        $uq = X3::db()->query("SELECT id, CONCAT(name,' ',surname) AS username, role FROM data_user WHERE status>0 AND id<>".X3::user()->id);
        $users = array();
        while($u = mysql_fetch_assoc($uq))
            $users[$u['id']] = $u['role']=='admin'?X3::translate('Администратор').'#'.$u['id']:$u['username'];
        return $users;
    }
    
    public function actionRead() {
        if(!IS_AJAX) throw new X3_404();
        $id = (int)$_GET['id'];
        Message::update(array('status'=>'1'),array('user_to'=>X3::user()->id,'id'=>$id));
        exit;
    }
    
    public function beforeValidate() {
        if($this->created_at == 0)
            $this->created_at = time();
    }    
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            Forum_Uploads::delete(array('message_id'=>$model->id));
        }
        parent::onDelete($tables, $condition);
    }    
}

?>
