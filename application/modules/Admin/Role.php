<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Roles
 *
 * @author Soul_man
 */
class Admin_Role extends X3_Module_Table {
    
    public $encoding = 'UTF-8';
    public $tableName = 'admin_role';
    public $_fields = array(
        'role'=>array('string[255]','primary'),
        'title'=>array('string[255]','language'),
    );
    public function fieldNames() {
        return array(
            'title'=>'Описание',
            'role'=>'Название',
        );
    }     
}

?>
