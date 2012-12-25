<?php

class Admin extends X3_Module {
    
    public function filter() {
        return array(
            'allow'=>array(
                '*'=>array('add'),
                'admin'=>array('add','send')
            ),
            'deny'=>array(
                '*'
            ),
            'handle'=>'redirect:/user/login.html'
        );
    }    
    
    public function actionSend() {
        if(IS_AJAX && isset($_POST['email'])){
            $email = $_POST['email'];
           
            $user = new User();
            $user->password = $email . "password";
            $user->role = 'admin';
            $user->email = $email;
            $user->status = 0;
            if(!$user->save()){
                echo json_encode(array('status'=>'error','message'=>X3::translate('Введен не верный E-Mail адрес')));
                exit;
            }
            
            $link = base64_encode($user->akey . "|" . X3::user()->id);
            if(TRUE === ($msg=Notify::sendMail('welcomeAdmin', array('link'=>$link),$email)))
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
        if(NULL === ($user = User::get(array('akey'=>$key[0]))))
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
            $user->role = 'admin';
            $user->status = 1;
            $errors = $user->getTable()->getErrors();
            if(empty($errors) && $user->save()){
                Notify::sendMessage('');
                if(X3::user()->isGuest()){
                    $u = new UserIdentity($user->email, $post['password']);
                    if($u->login())
                        $this->redirect('/');
                }
                $this->redirect('/admins/');
            }
        }
        $this->template->render('@views:user:addadmin.php',array('user'=>$user));
    }
    
}