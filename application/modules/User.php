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
        'image' => array('file', 'default' => 'NULL', 'allowed' => array('jpg', 'gif', 'png', 'jpeg'), 'max_size' => 10240),
        'name'=>array('string[255]','default'=>''),
        'surname'=>array('string[255]','default'=>''),
        'gender'=>array('enum["Мужской","Женский"]','default'=>'Мужской'),
        'email'=>array('email','unique'), //as login
        'password'=>array('string[255]','password'),
        'role'=>array('string[255]','default'=>'user'),
        'akey'=>array('string[255]'),
        'date_of_birth'=>array('datetime','default'=>'0'),
        'lastbeen_at'=>array('datetime','default'=>'0'),
        'status'=>array('integer[1]','unsigned','default'=>'0')
    );
    
    public function fieldNames() {
        return array(
            'name'=>X3::translate('Имя'),
            'surname'=>X3::translate('Фамилия'),
            'password'=>X3::translate('Пароль'),
            'email'=>'E-mail',
            'role'=>X3::translate('Роль'),
            'lastbeen_at'=>X3::translate('Последнее посещение'),
        );
    }

    public function filter() {
        return array(
            'allow'=>array(
                '*'=>array('login'),
                'user'=>array('edit','logout','password'),
                'admin'=>array('edit','admins','logout','password','delete')
            ),
            'deny'=>array(
                '*'
            ),
            'handle'=>'redirect:/user/login.html'
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
    public function actionEdit() {
        $id = X3::app()->user->id;
        exit;
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
    
    public function actionAdmins() {
        $count = User::num_rows(array('role'=>'admin','status'));
        $models = User::get(array('role'=>'admin','status'));
        $this->template->render('admins',array('count'=>$count,'models'=>$models));
    }

    public function actionDelete() {
        if(!X3::user()->isAdmin() || !isset($_GET['id']))
            $this->redirect('/');
        $id = (int)$_GET['id'];
        User::deleteByPk($id);
        $this->redirect('/admins/');
    }
    
    public function actionLogin() {
        if(!X3::user()->isGuest())
            $this->redirect('/');
        $error = false;
        $u = array('email'=>'','password'=>'');
        if(isset($_POST['User'])){
            $u = array_extend($u,$_POST['User']);
            $u['email'] = mysql_real_escape_string($u['email']);
            $u['password'] = mysql_real_escape_string($u['password']);
            $user = new UserIdentity($u['email'], $u['password']);
            if($user->login()){
                $url = $_SERVER['HTTP_REFERER'];
                if(strpos($url,'user/login')!==false)
                    $url = '';
                $this->controller->redirect($url);
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
        if($this->getTable()->getIsNewRecord())
            $this->akey = md5(time().rand(10,99)).rand(10,99);
    }

    public function afterValidate() {
        if(isset($_POST['User']['password']) && $_POST['User']['password']!='' && !isset($_POST['notouch']))
            $this->password = md5($_POST['User']['password']);
    }

    public function afterSave($bNew=false) {
        if(!$this->getTable()->getIsNewRecord() && X3::app()->user->id == $this->id){
            if(!is_null($this->name))
                X3::app()->user->name = $this->name;
            if(!is_null($this->role))
                X3::app()->user->role = $this->role;
            if(!is_null($this->email))
                X3::app()->user->email = $this->email;
        }
        return TRUE;
    }

}
?>
