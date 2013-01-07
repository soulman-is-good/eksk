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
        'phone'=>array('string','unique'), //as login
        'password'=>array('string[255]','password'),
        'role'=>array('string[255]','default'=>'user'),
        'akey'=>array('string[255]'),
        'date_of_birth'=>array('datetime','default'=>'0'),
        'lastbeen_at'=>array('datetime','default'=>'0'),
        'status'=>array('integer[1]','unsigned','default'=>'0')
    );
    
    public function onValidate($attr,$pass) {
        $pass = false;
        if($attr == 'phone') {
            //TODO: phone validation
            if(preg_match("/^[0-9]{3} [0-9]{3}.[0-9]{2}.[0-9]{2}$/",$this->$attr) == false){
                $this->addError($attr,'Не корректно указан номер телефона.');
            }
        }
    }
    
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
                '*'=>array('login','deny','add'),
                'user'=>array('index','edit','logout','password','list'),
                'ksk'=>array('index','edit','logout','password','list','send'),
                'admin'=>array('index','edit','admins','logout','password','delete','list','block','send')
            ),
            'deny'=>array(
                '*'=>array('*')
            ),
            'handle'=>'redirect:/user/login.html'
        );
    }
    
    public function isOnline() {
        $online = null;
        if(X3::app()->hasComponent('mongo') && X3::mongo()!=null){
            $online = X3::mongo()->query(array('online:findOne'=>array('user_id'=>$this->id)));
        }
        return !is_null($online);
    }
    
    public function actionIndex() {
        if(isset($_GET['id']))
            $id = (int)$_GET['id'];
        else
            $id = X3::user()->id;
        $user = User::getByPk($id);
        if($user == null)
            throw new X3_404();
        $this->template->render('@views:site:index.php',array('user'=>$user));
    }
    /**
     * renders user list
     */
    public function actionList() {
        $count = User::num_rows(array('role'=>'user'));
        $models = User::get(array('role'=>'user'));
        //TODO: If we are user or ksk
        $this->template->render('users',array('models'=>$models,'count'=>$count));
    }
    
    public function actionEdit() {
        $id = X3::app()->user->id;
        $user = User::getByPk($id);
        if(isset($_POST['User'])){
            if($user->save()){
                $this->redirect('/');
            }
        }
        $this->template->render('settings',array('user'=>$user));
    }
    
    public function actionBlock() {
        if(!X3::user()->isAdmin() || !isset($_GET['id']))
            $this->redirect('/');
        $id = (int)$_GET['id'];
        User::update(array('status'=>'2'),array('id'=>$id));
        $this->redirect('/admins/');
    }
    
    public function actionAdmins() {
        $count = User::num_rows(array('role'=>'admin','status'=>array('>'=>'0')));
        $models = User::get(array('role'=>'admin','status'=>array('>'=>'0')));
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
            if(TRUE===($error = $user->login())){
                $this->refresh();
            }
        }
        $this->template->render('login',array('error'=>$error,'user'=>$u));
    }
    
    public function actionLogout() {
        if(X3::app()->user->logout()){
            $this->controller->redirect('/');
        }
    }
    
    /**
     * Add user logic
     * @throws X3_404
     */
    public function actionSend() {
        if(IS_AJAX && isset($_POST['email'])){
            $email = $_POST['email'];
           
            $user = new User();
            $user->password = $email . "password";
            $user->role = 'user';
            $user->email = $email;
            $user->status = 0;
            if(!$user->save()){
                echo json_encode(array('status'=>'error','message'=>X3::translate('Введен не верный E-Mail адрес')));
                exit;
            }
            
            $link = base64_encode($user->akey . "|" . X3::user()->id);
            if(TRUE === ($msg=Notify::sendMail('welcomeUser', array('link'=>$link),$email)))
                echo json_encode(array('status'=>'ok','message'=>X3::translate('Письмо успешно отправлено')));
            else
                echo json_encode(array('status'=>'error','message'=>$msg));
            exit;
        }
        throw new X3_404();
    }
    
    public function actionDeny() {
        if(!isset($_GET['key']))
            throw new X3_404();
        $key = base64_decode($_GET['key']);
        $key = explode('|',$key);
        User::delete(array('akey'=>$key[0]));
        $this->redirect('/');
    }
    
    public function actionAdd() {
        if(!isset($_GET['key']))
            throw new X3_404();
        $key = base64_decode($_GET['key']);
        $key = explode('|',$key);
        if(NULL === ($user = User::get(array('akey'=>$key[0]),1)))
            throw new X3_404();
        if(isset($_POST['User'])){
            $post = $_POST['User'];
            $user->getTable()->acquire($post);
            if($user->password == ''){
                $user->addError('password', X3::translate('Нужно задать пароль'));
            }
            if($user->name == ''){
                $user->addError('name', X3::translate('Введите Ваше имя'));
            }
            if($user->surname == ''){
                $user->addError('surname', X3::translate('Введите Вашу фамилию'));
            }
            $user->status = 1;
            $errors = $user->getTable()->getErrors();
            if(empty($errors) && $user->save()){
                Notify::sendMessage("Пользователь $user->name $user->surname ($user->email) зарегистрировался на сайте.");
                if(X3::user()->isGuest()){
                    $u = new UserIdentity($user->email, $post['password']);
                    if($u->login())
                        $this->redirect('/');
                }
                $this->redirect('/');
            }
        }
        $this->template->render('@views:user:adduser.php',array('user'=>$user));
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
