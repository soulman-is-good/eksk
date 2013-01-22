<?php
/**
 * Description of User_Settings
 *
 * @author Soul_man
 */
class User_Rank extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'user_rank';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'user_ksk' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'ip' => array('string','default'=>'NULL'),
        'rank' => array('integer[11]','default'=>'0'),
    );
    
    public static function add($id,$rank=0) {
        $rank = $rank % 101;
        if($rank>0 && NULL==self::get(array('user_id'=>X3::user()->id,'user_ksk'=>$id),1)){
            $R = new self;
            $R->user_id = X3::user()->id;
            $R->user_ksk = $id;
            $R->rank = $rank;
            $R->ip = $_SERVER['REMOTE_ADDR'];
            $R->save();
        }
        $vote = X3::db()->fetch("SELECT SUM(`rank`)/COUNT(`id`) AS `rank` FROM `user_rank` WHERE user_ksk=$id");
        return (int)$vote['rank'];
    }
}

?>
