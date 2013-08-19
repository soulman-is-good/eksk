<?php
/**
 * Description of User_Settings
 *
 * @author Soul_man
 */
class User_Stat extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'user_stat';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'ip' => array('string','default'=>'NULL'),
        'agent' => array('string','default'=>'NULL'),
        'referer' => array('string','default'=>'NULL'),
        'action' => array('string','default'=>'NULL'),
        'login_at' => array('datetime'),
    );
    
    public static function add() {
        $R = new self;
        $R->user_id = X3::user()->id;
        $R->ip = $_SERVER['REMOTE_ADDR'];
        $R->agent = substr($_SERVER['HTTP_USER_AGENT'],0,255);
        $R->referer = substr($_SERVER['HTTP_REFERER'],0,255);
        $R->action = substr($_SERVER['REQUEST_URI'],0,255);
        $R->login_at = time();
        $R->save();
        return $R;
    }
}

?>
