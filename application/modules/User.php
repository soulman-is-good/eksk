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
class User extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_user';
    public static $balance = null;
    
    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'name'=>array('string[255]','default'=>''),
        'login'=>array('string[64]','unique'),
        'email'=>array('email','unique'),
        'password'=>array('string[255]','password'),
        'role'=>array('string[255]','default'=>'user'),
        'lastbeen_at'=>array('integer[10]','unsigned','default'=>'0','datetime'),
        'status'=>array('integer[1]','unsigned','default'=>'1') //0-deleted, 1-active, 2-activation, 3-banned
    );
    
    public function fieldNames() {
        return array(
            'name'=>'Имя',
            'login'=>'Логин',
            'password'=>'Пароль',
            'email'=>'E-mail',
            'role'=>'Роль',
            'lastbeen_at'=>'Последнее посещение',
        );
    }

    public function filter() {
        return array(
            'allow'=>array(
                '*'=>array('login'),
                'user'=>array('settings','logout','password'),
                'admin'=>array('settings','logout','password','cart','balance','history','credit')
            ),
            'deny'=>array(
                '*'
            ),
            'handle'=>'redirect:user/login'
        );
    }

    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr,$single=false,$class=__CLASS__) {
        return parent::get($arr,$single,$class);
    }
    public static function getByPk($pk,$class=__CLASS__) {
        return parent::getByPk($pk,$class);
    }
    public function actionSettings() {
        $id = X3::app()->user->id;
        $user = $this->table->select('*')->where('id='.$id)->asObject(true);
        $success = null;
        if(isset($_POST['User'])){
            if(trim($_POST['User']['username'])===''){
                $user->addError('username','Введите ваш email в поле \'Логин\'');
            }
            else
                $user->username = $_POST['User']['username'];
            if(trim($_POST['User']['password'])!==''){
                if(md5($_POST['User']['password'])!==$user->password){
                    $user->addError('password','Пароль введен не верно!');
                }elseif(trim($_POST['newpassword'])===''){
                    $user->addError('password','Введите новый пароль!');
                }elseif(trim($_POST['repeatnewpassword'])===''){
                    $user->addError('password','Нужно ввести повтор нового пароля.');
                }elseif($_POST['repeatnewpassword']!==$_POST['newpassword'])
                    $user->addError('password','Пароли не совпадают');
                else{
                    unset($_POST['User']['password']); //for sake of admin part
                    $user->password = md5($_POST['newpassword']);
                }
            }elseif($_POST['newpassword']!=='' || $_POST['repeatnewpassword']!==''){
                $user->addError('password','Нужно ввести старый пароль, перед тем как его менять.');
            }
            $user->n_order = isset($_POST['User']['n_order'])?1:0;
            $user->n_status = isset($_POST['User']['n_status'])?1:0;
            $user->n_pay = isset($_POST['User']['n_pay'])?1:0;
            if($user->save()){
                $success = 'Данные успешно сохранены!';
            }
        }
        $this->template->render('settings',array('user'=>$user,'success'=>$success));
    }

    public function actionLogin() {
        $error = false;
        $u = array('uid'=>'','password'=>'');
        if(isset($_POST['User'])){
            $u = array_extend($u,$_POST['User']);
            $u['login'] = mysql_real_escape_string($u['login']);
            $u['password'] = mysql_real_escape_string($u['password']);
            $user = new UserIdentity($u['login'], $u['password']);
            if($user->login()){
                $this->controller->redirect($_SERVER['HTTP_REFERER']);
            }else{
                $error = 'Логин или пароль не верны.';
            }
        }
        $this->template->render('login',array('error'=>$error,'user'=>$u));
    }
    
    public function actionLogout() {
        if(X3::app()->user->logout()){
            $this->controller->redirect('/');
        }
    }

    public function beforeValidate() {
        if(isset($this->id) && (!isset($_POST['User']['password']) || $_POST['User']['password']=='')){
            $user = User::newInstance()->table->select('password')->where("id=$this->id")->asArray(true);
            $this->password = $user['password'];
            $_POST['notouch']=true;
        }
    }

    public function afterValidate() {
        if(isset($_POST['User']['password']) && $_POST['User']['password']!='' && !isset($_POST['notouch']))
            $this->password = md5($_POST['User']['password']);
    }

    public function afterSave($bNew=false) {
        if(X3::app()->user->id == $this->id){
            if(!is_null($this->name))
                X3::app()->user->name = $this->name;
            if(!is_null($this->role))
                X3::app()->user->role = $this->role;
            if(!is_null($this->email))
                X3::app()->user->email = $this->email;
        }
        $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list.user";
        if(file_exists($path))
            X3::app()->cache->flush($path);
        return TRUE;
    }

}
?>
