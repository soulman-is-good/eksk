<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Menu
 *
 * @author Soul_man
 */
class Admin_Menu extends X3_Module_Table {
    
    public $encoding = 'UTF-8';
    public $tableName = 'admin_menu';
    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'parent_id'=>array('integer[10]','unsigned','index','default'=>'NULL','ref'=>array('Admin_Menu'=>'id','default'=>'title','level'=>'0')),
        'role'=>array('string[255]','default'=>'admin','orderable'),
        'title'=>array('string[255]','orderable'),
        'link'=>array('string[255]','default'=>'/'),
        'weight'=>array('integer[10]','default'=>'0','orderable'),
        'status'=>array('boolean','default'=>'1','orderable'),
    );
    public function fieldNames() {
        return array(
            'parent_id'=>'Родитель',
            'title'=>'Заголовок',
            'link'=>'Ссылка',
            'role'=>'Видят только',
            'weight'=>'Порядок',
            'status'=>'Видимость',
        );
    }
    public function moduleTitle() {
        return 'Административное меню';
    }
    public function getDefaultScope() {
        return array(
            '@order'=>array('weight')
        );
    }
}

?>
