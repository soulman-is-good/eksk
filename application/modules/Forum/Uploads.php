<?php
/**
 * Description of Uploads
 * @property string $file_id primary key
 * @property integer $message_id message id
 * @property integer $created_at unix timestamp 
 * @author Soul_man
 */
class Forum_Uploads extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'forum_uploads';

    public $_fields = array(
      'file_id'=>array('string[128]','primary'),
      'message_id'=>array('integer[10]','unsigned','index','ref'=>array('Message','id','default'=>'content')),
      'created_at'=>array('integer[10]','unsigned','default'=>'0')
    );
    public function fieldNames() {
        return array(
            'created_at' => 'Дата',
            'file_id' => 'Файл',
            'message_id' => 'Сообщение',
        );
    }
    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }
    
}

?>
