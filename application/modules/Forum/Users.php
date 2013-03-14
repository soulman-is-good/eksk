<?php

/**
 * Description of Default
 *
 * @author Soul_man
 */
class Forum_Users extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'forum_users';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'forum_id' => array('integer[10]', 'unsigned', 'index', 'ref' => array('Forum', 'id', 'default' => 'title')),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref' => array('User', 'id', 'default' => 'email')),
        'updated_at' => array('datetime', 'default' => '0'),
    );

    public function fieldNames() {
        return array(
            'forum_id' => X3::translate('Тема'),
            'user_id' => X3::translate('Кто читал'),
            'updated_at' => X3::translate('Когда читал'),
        );
    }
    
    public function beforeSave() {
        $this->updated_at = time();
    }    
}

?>
