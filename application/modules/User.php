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
        'kskname'=>array('string[255]','default'=>''),
        'surname'=>array('string[255]','default'=>''),
        'ksksurname'=>array('string[255]','default'=>''),
        'duty'=>array('string[255]','default'=>''),
        'gender'=>array('enum["Мужской","Женский"]','default'=>'Мужской'),
        'email'=>array('email','unique'), //as login
        'phone'=>array('string','default'=>''), //as login
        'password'=>array('string[5|50]','password'),
        'role'=>array('string[255]','default'=>'user'),
        'akey'=>array('string[255]','default'=>''),
        'date_of_birth'=>array('integer[11]','default'=>'0'),
        'rank'=>array('integer[11]','default'=>'0'),
        'lastbeen_at'=>array('datetime','default'=>'0'),
        'created_at'=>array('datetime','default'=>'0'),
        'status'=>array('integer[1]','unsigned','default'=>'0'),
        //unused
        'password_old'=>array('string[6|50]','password','default'=>'','unused'),
        'password_new'=>array('string[6|50]','password','default'=>'','unused'),
        'password_repeat'=>array('string[50]','password','default'=>'','unused'),
        'captcha'=>array('string[255]','default'=>'','unused'),
        'iagree'=>array('boolean','default'=>'0','unused'),
    );
    
    public function onValidate($attr,$pass) {
        $pass = false;
        if(isset($this->_fields[$attr]) && in_array('xss',$this->_fields[$attr]) && trim($this->$name)!=''){
            
        }
        if($attr == 'phone' && trim($this->$attr) != '') {
            //TODO: phone validation
            $id = $this->id;
            $phone = preg_replace("/^\+7/","",trim($this->$attr));
            $phone = trim(preg_replace("/[\(\) ]/","",trim($phone)));
            $phone = array(substr($phone, 0,3),substr($phone, 3,3),substr($phone, 6,2),substr($phone, 8,2));
            //var_dump($phone);die;
            if(preg_match("/^[0-9]{3} [0-9]{3}.{0,1}[0-9]{2}.{0,1}[0-9]{2}$/",$this->$attr) == false){
                $this->addError($attr,X3::translate('Не корректно указан номер телефона.'));
            }else if(X3::db()->count("SELECT id FROM data_user u WHERE u.phone REGEXP '{$phone[0]} {$phone[1]}.{0,1}{$phone[2]}.{0,1}{$phone[3]}' AND u.id<>'$id'")>0){
                $this->addError($attr,X3::translate('Такой номер телефона уже используется.'));
            }
        }
    }
    
    public function fieldNames() {
        $known = X3::translate('Я ознакомлен(а) и соглас(ен/на) с [правилами] сайта');
        if(preg_match("/\[(.+)\]/", $known,$m)>0){
            $known = str_replace($m[0], '<a href="/page/rules.html" target="_blank">'.$m[1].'</a>', $known);
        }
        return array(
            'iagree'=>$known,
            'image'=>X3::translate('Аватарка'),
            'name'=>X3::translate('Имя'),
            'kskname'=>X3::translate('Название КСК'),
            'duty'=>X3::translate('Должность'),
            'surname'=>X3::translate('Фамилия'),
            'email'=>'E-mail',
            'phone'=>X3::translate('Телефон') . ' +7',
            'password'=>X3::translate('Пароль'),
            'password_old'=>X3::translate('Старый пароль'),
            'password_new'=>X3::translate('Новый пароль'),
            'password_repeat'=>X3::translate('Повторите новый пароль'),
            'gender'=>X3::translate('Пол'),
            'role'=>X3::translate('Роль'),
            'lastbeen_at'=>X3::translate('Последнее посещение'),
            'date_of_birth'=>X3::translate('Дата рождения'),
        );
    }

    public function filter() {
        return array(
            'allow'=>array(
                '*'=>array('login','logout','deny','add','rank'),
                'user'=>array('index','edit','logout','password','list'),
                'ksk'=>array('index','edit','logout','password','list','send','block','unblock'),
                'admin'=>array('index','edit','admins','logout','password','delete','list','block','send','block','unblock')
            ),
            'deny'=>array(
                '*'=>array('*')
            ),
            'handle'=>'redirect:/user/login.html'
        );
    }
    
    public function getAvatar($size = '100x100') {
        if($this->image=='' || $this->image==null || !is_file('uploads/User/'.$this->image))
            return '/images/default.png';
        if($size)
            return '/uploads/User/'.$size.'/'.$this->image;
    }
    
    public static function isMyNeibor($id) {
        $i = X3::user()->id;
        $i = X3::db()->fetch("SELECT ($i IN (SELECT a1.user_id FROM user_address a1, user_address a2 
WHERE a2.user_id=$id AND a1.user_id<>a2.user_id AND `a2`.`city_id` = a1.city_id AND `a2`.`region_id` = a1.region_id AND `a2`.`house` = a1.house) AND (SELECT role FROM data_user WHERE id=$id)='user') AS `ismyksk`");
        return $i['ismyksk'] == '1';
    }
    
    public static function isMyKsk($id) {
        $i = X3::user()->id;
        $i = X3::db()->fetch("SELECT ($i IN (SELECT a1.user_id FROM user_address a1, user_address a2 
WHERE a2.user_id=$id AND a1.user_id<>a2.user_id AND `a2`.`city_id` = a1.city_id AND `a2`.`region_id` = a1.region_id AND `a2`.`house` = a1.house AND a2.status=1) AND (SELECT role FROM data_user WHERE id=$id)='ksk') AS `ismyksk`");
        return $i['ismyksk'] == '1';
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
        if($user == null || ($user->role == 'admin' && !X3::user()->isAdmin()) || (X3::user()->isUser() && $user->role=='ksk' && !self::isMyKsk($id)) || (X3::user()->isUser() && $user->role=='user' && !self::isMyNeibor($id)))
            throw new X3_404();
        $this->template->render('@views:site:index.php',array('user'=>$user));
    }
    /**
     * renders user list
     */
    public function actionList() {
        $type = 'user';
        $id = X3::user()->id;
        if(isset($_GET['type']))
            $type = $_GET['type'];
        if(X3::user()->isAdmin()){
            $query = array(
                '@condition'=>array('role'=>$type,'id'=>array('<>'=>$id)),
            );
            $count = User::num_rows($query);
            $models = User::get($query);
        }elseif(X3::user()->isKsk()){
            $query = array(
                '@condition'=>array('role'=>'user','id'=>array('IN'=>"(SELECT a1.user_id FROM user_address a1, user_address a2 
WHERE a2.user_id=$id AND a1.user_id<>a2.user_id AND `a2`.`city_id` = a1.city_id AND `a2`.`region_id` = a1.region_id AND `a2`.`house` = a1.house AND a2.status=1)")),
                '@group'=>'id'
            );
            $count = User::num_rows($query);
            $models = User::get($query);
        }elseif(X3::user()->isUser()){
            $query = array(
                '@condition'=>array('role'=>$type,'id'=>array('IN'=>"(SELECT a1.user_id FROM user_address a1, user_address a2 
WHERE a2.user_id=$id AND a1.user_id<>a2.user_id AND `a2`.`city_id` = a1.city_id AND `a2`.`region_id` = a1.region_id AND `a2`.`house` = a1.house AND a1.status=1)")),
                '@group'=>'id'
            );
            $count = User::num_rows($query);
            echo X3::db()->getErrors();
            $models = User::get($query);
        }
        $this->template->render('users',array('models'=>$models,'count'=>$count,'type'=>$type));
    }
    
    public function actionEdit() {
        $id = X3::app()->user->id;
        $user = User::getByPk($id);
        $hash = false;
        if($user == null)
            throw new X3_404();
        $profile = User_Settings::get(array('user_id'=>$user->id),1);
        if($profile == null){
            $profile = new User_Settings();
            $profile->user_id = $id;
        }
        if(isset($_POST['User'])){
            if(isset($_POST['User']['date_of_birth']))
                $_POST['User']['date_of_birth'] = mktime(12,0,0,$_POST['User']['date_of_birth'][1],$_POST['User']['date_of_birth'][0],$_POST['User']['date_of_birth'][2]);
            $user->getTable()->acquire($_POST['User']);
            if($user->name == ''){
                if($user->type == 'ksk')
                    $user->addError('name', X3::translate('Введите название КСК'));
                else
                    $user->addError('name', X3::translate('Введите Ваше имя'));
            }
            if(X3::user()->isUser() && $user->surname == ''){
                $user->addError('surname', X3::translate('Введите Вашу фамилию'));
            }
            if(isset($_POST['User']['date_of_birth'])){
                $h = new Upload($user,'image');
                if(isset($_POST['User']['image_delete'])){
                    Uploads::cleanUp('User', $user->image);
                    $user->image = null;
                }elseif(!$h->message=='' || !$h->save()){
                    $user->addError('image', $h->message);
                }
            }
            if(!$user->save()){
                if(isset($_POST['User']['phone']))
                    $hash = '#login-settings';
                //if(X3::user()->superAdmin){
                    //var_dump($user->getTable()->getErrors());
                    //exit;
                //}
            }
        }
        if(isset($_POST['User_Settings'])){
            $data = $_POST['User_Settings'];
            //$_POST['User']['date_of_birth'] = mktime(12,0,0,$_POST['User']['date_of_birth'][1],$_POST['User']['date_of_birth'][0],$_POST['User']['date_of_birth'][2]);
            $profile->getTable()->acquire($data);
            if(!$profile->save()){
            }
        }
        $address_errors = array();
        if(isset($_POST['Address'])){
            $data = $_POST['Address'];
            foreach($data as $adr){
                if(isset($adr['delete'])){
                    User_Address::deleteByPk($adr['id']);
                }else if(trim($adr['flat'])!='' && trim($adr['house'])!=''){
                    if($adr['id']>0){
                        $address = User_Address::get(array('user_id'=>$id,'id'=>$adr['id']),1);
                    }else{
                        $address = new User_Address;
                        if(X3::user()->isKsk() && X3::db()->count("SELECT id FROM user_address WHERE user_id=$id AND house='".trim($adr['house'])."'")>0)
                            continue;
                        if(X3::user()->isKsk() && (FALSE!=($e = X3::db()->fetch("SELECT u.name AS `name` FROM user_address a INNER JOIN data_user u ON u.id=a.user_id WHERE role='ksk' AND user_id<>$id AND house='".trim($adr['house'])."'")))){
                            $address_errors[] = strtr(X3::translate("Дом номер {number} зарегистрирован за '{ksk}'"),array('{number}'=>$adr['house'],'{ksk}'=>$e['name']));
                            continue;
                        }
                    }
                    $address->user_id = $id;
                    $address->getTable()->acquire($adr);
                    if(!$address->save()){
                        if(X3::user()->superAdmin)
                            var_dump($address->getTable()->getErrors());
                    }
                }
            }
        }
        if(isset($_POST['Change'])){
            $data = $_POST['Change'];
            if($data['password_old']!='' && $data['password_new']!='' && $data['password_repeat']!=''){
                $hash = '#login-settings';
                if(md5($data['password_old']) != $user->password)
                    $user->addError('password_old',X3::translate('Пароли не совпадают'));
                if($data['password_new'] != $data['password_repeat'])
                    $user->addError('password_repeat',X3::translate('Пароли не совпадают'));
                $ers = $user->getTable()->getErrors();
                if(empty($ers)){
                    $user->password = md5($data['password_new']);
                    if($user->save()){
                        $hash = false;
                    }
                }
            }elseif($data['password_old']!='' || $data['password_new']!='' || $data['password_repeat']!=''){
                $hash = '#login-settings';
                if($data['password_old'] == '')
                    $user->addError('password_old',X3::translate('Поле `{attribute}` не должно быть пустым', array('{attribute}' => $user->fieldName('password_old'))));
                if($data['password_new'] == '')
                    $user->addError('password_new',X3::translate('Поле `{attribute}` не должно быть пустым', array('{attribute}' => $user->fieldName('password_new'))));
                if($data['password_repeat'] == '')
                    $user->addError('password_repeat',X3::translate('Поле `{attribute}` не должно быть пустым', array('{attribute}' => $user->fieldName('password_repeat'))));
            }
        }
        
        $this->template->render('edit',array('user'=>$user,'profile'=>$profile,'hash'=>$hash,'adrerrors'=>$address_errors));
    }
    
    public function actionBlock() {
        if(X3::user()->isUser() || !isset($_GET['id']))
            $this->redirect('/');
        $id = (int)$_GET['id'];
        $q = array('id'=>$id);
        if(X3::user()->isKsk())
            $q['role'] = 'user';
        User::update(array('status'=>'2'),$q);
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function actionUnblock() {
        if(X3::user()->isUser() || !isset($_GET['id']))
            $this->redirect('/');
        $id = (int)$_GET['id'];
        $q = array('id'=>$id);
        if(X3::user()->isKsk())
            $q['role'] = 'user';
        User::update(array('status'=>'1'),$q);
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function actionRank(){
        if(X3::user()->isUser() && isset($_GET['id']) && ($id = (int)$_GET['id'])>0 && User::isMyKsk($id)){
            $rank = (int)$_GET['mark'];
            echo User_Rank::add($id,$rank);
            exit;
        }
        throw new X3_404();
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
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function actionLogin() {
        if(!X3::user()->isGuest())
            $this->redirect('/');
        $error = false;
        $u = array('email'=>'','password'=>'');
        $user = new User;
        $address = new User_Address;
        if(isset($_POST['captcha'])){
            $pass = true;
            if(md5(strtolower($_POST['captcha'])) != X3::user()->captcha['text']){
                $user->addError('captcha', X3::translate('Неверный код с картинки'));
                $pass = false;
            }
            $user->getTable()->acquire($_POST['User']);
            $address->getTable()->acquire($_POST['User_Address']);
            $user->role = 'user';
            $user->status = 0;
            $address->user_id = 1;
            if(!$user->iagree){
                $user->addError('iagree', X3::translate('Вы должны быть согласны с правилами сайта'));
            }
            if($user->password != $user->password_repeat){
                $user->addError('password_repeat', X3::translate('Пароли не совпадают'));
            }
            if(trim($address->flat)=='' || preg_match("/^[0-9A-Za-z]+$/", $address->flat)==0){
                $address->addError('flat', X3::translate('Нужно ввести номер квартиры'));
                $pass = false;
            }
            if(NULL == City_Region::getByPk($address->region_id)){
                $address->addError('region_id', X3::translate('Нужно выбрать улицу'));
                $pass = false;
            }
            if(($a = array_pop(X3::db()->fetch("SELECT '$address->house' IN (SELECT house FROM user_address WHERE region_id='$address->region_id')")))==0){
                var_dump("SELECT '$address->house' IN (SELECT id FROM user_address WHERE region_id='$address->region_id')",$a);exit;
                $address->addError('house', X3::translate('Нужно выбрать дом'));
                $pass = false;
            }
            if($user->validate() && $pass && $address->getTable()->validate()){
                if($user->save()){
                    $address->user_id = $user->id;
                    if($address->save()){
                        $link = base64_encode($user->akey . "|" . X3::user()->id);
                        Notify::sendMail('welcomeUser',array('link'=>$link),$user->email);
                        $this->redirect('/page/success.html');
                    }
                }
            }
        }
        if(!isset($_POST['captcha']) && isset($_POST['User'])){
            $u = array_extend($u,$_POST['User']);
            $u['email'] = mysql_real_escape_string($u['email']);
            $u['password'] = mysql_real_escape_string($u['password']);
            $userI = new UserIdentity($u['email'], $u['password']);
            $error = $userI->login();
            if(!is_string($error)){
                $this->refresh();
            }
        }
        $this->template->render('login',array('error'=>$error,'user'=>$user,'address'=>$address));
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
            $type = isset($_POST['type']) && $_POST['type'] == 'ksk'?'ksk':'user';
            $user = new User();
            $user->password = $email . "password";
            $user->role = $type;
            $user->email = $email;
            $user->status = 0;
            $type = ucfirst($type);
            if(!$user->save()){
                $errs = $user->getTable()->getErrors();
                if(isset($errs['email']))
                    echo json_encode(array('status'=>'error','message'=>$errs['email'][0]));
                else
                    echo json_encode(array('status'=>'error','message'=>'Возникла неизвестная ошибка. Обратитесь к Администратору'));
                exit;
            }
            
            $link = base64_encode($user->akey . "|" . X3::user()->id);
            if(TRUE === ($msg=Notify::sendMail('welcome'.$type, array('link'=>$link),$email)))
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
        $address = null;
        if($user->role == 'ksk'){
            $address = new User_Address();
            $address->user_id = $user->id;
        }
        if(isset($_POST['User'])){
            $post = $_POST['User'];
            $user->getTable()->acquire($post);
            if($user->password == ''){
                $user->addError('password', X3::translate('Нужно задать пароль'));
            }
            if($user->name == ''){
                if($user->role == 'ksk')
                    $user->addError('name', X3::translate('Введите название КСК'));
                else
                    $user->addError('name', X3::translate('Введите Ваше имя'));
            }
            if($user->surname == '' && $user->role != 'ksk'){
                $user->addError('surname', X3::translate('Введите Вашу фамилию'));
            }            
            $user->status = 1;
            $errors = $user->getTable()->getErrors();
            if($user->role == 'ksk'){
                $address->getTable()->acquire($_POST['User_Address']);
                if(trim($address->house) == ''){
                    $address->addError('house', X3::translate('Нужно ввести дом'));
                }
                if(trim($address->flat) == ''){
                    $address->addError('flat', X3::translate('Нужно ввести квартиру'));
                }
                $address->status='0';
                $address->save();
            }
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
        $this->template->render('@views:user:adduser.php',array('user'=>$user,'address'=>$address));
    }

    public function beforeValidate() {
        if(isset($this->id) && (!isset($_POST['User']['password']) || $_POST['User']['password']=='')){
            $user = User::newInstance()->table->select('password')->where("id=$this->id")->asArray(true);
            $this->password = $user['password'];
            $_POST['notouch']=true;
        }
        if($this->getTable()->getIsNewRecord()){
            $this->created_at = time();
            $this->akey = md5(time().rand(10,99)).rand(10,99);
        }
    }

    public function afterValidate() {
        if(isset($_POST['User']['password']) && $_POST['User']['password']!='' && !isset($_POST['notouch']))
            $this->password = md5($_POST['User']['password']);
    }

    public function afterSave($bNew=false) {
        if(!$this->getTable()->getIsNewRecord() && X3::app()->user->id == $this->id){
            if(!is_null($this->name))
                X3::app()->user->fullname = $this->name . " " . $this->surname;
            if(!is_null($this->role))
                X3::app()->user->role = $this->role;
            if(!is_null($this->email))
                X3::app()->user->email = $this->email;
        }
        return TRUE;
    }
    
    public function getFullname(){
        if($this->role == 'admin')
            return X3::translate('Администратор') . "#" . $this->id;
        if($this->role == 'ksk')
            return $this->name;
        return $this->name . " " . $this->surname;
    }

        public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            Uploads::cleanUp($model, $model->image);
            Forum::delete(array('user_id'=>$model->id));
            User_Address::delete(array('user_id'=>$model->id));
            User_Settings::delete(array('user_id'=>$model->id));
            Message::delete(array(array('user_to'=>$model->id),array('user_from'=>$model->id)));
        }
        parent::onDelete($tables, $condition);
    }

}
?>
