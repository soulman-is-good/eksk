<?php

class Admin extends X3_Module {
    
    public function filter() {
        return array(
            'allow'=>array(
                '*'=>array('add'),
                'admin'=>array('add','send','links','list','edit')
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
                $errs = $user->getTable()->getErrors();
                if(isset($errs['email']))
                    echo json_encode(array('status'=>'error','message'=>$errs['email'][0]));
                else
                    echo json_encode(array('status'=>'error','message'=>'Возникла неизвестная ошибка. Обратитесь к Администратору'));
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
    
    
    /**
     * Admin tools
     */
    
    public function actionLinks() {
        if(!IS_AJAX) throw new X3_404();
        if(X3::user()->superAdmin){
            echo json_encode(array(
                '/admin/list/module/Menu'=>'Меню',
                '/admin/list/module/Page'=>'Тестовые страницы',
                '/admin/list/module/Notify'=>'Письма',
                '/admin/list/module/City'=>'Города',
                '/admin/list/module/City_Region'=>'Улицы',
                '/admin/list/module/SysSettings'=>'Настройки',
            ));
        }
        exit;
    }
    
    public function actionList() {
        if(!X3::user()->superAdmin)
            throw new X3_404();
        $action = strtolower($_GET['module']);
        $path = X3::app()->getPathFromAlias("@views:admin:sudo:$action.php");
        if(is_file($path)){
            $this->template->render("sudo/$action",array('class'=>$class));
            exit;
        }
        $class = ucfirst($_GET['module']);
        $module = X3_Module::getInstance($class);
        $scope = $module->getDefaultScope();
        $count = X3_Module_Table::num_rows($scope,$class);
        $paginator = new Paginator($class."-Admin", $count,null,false);
        $scope['@limit'] = $paginator->limit;
        $scope['@offset'] = $paginator->offset;
        $models = X3_Module_Table::get($scope,0,$class,1);
        $this->template->render('sudo/list',array('models'=>$models,'module'=>$module,'paginator'=>$paginator,'count'=>$count,'class'=>$class));
    }
    
    public function actionView() {
        if(!X3::user()->superAdmin)
            throw new X3_404();
        $action = strtolower($_GET['module']);
        $path = X3::app()->getPathFromAlias("@views:admin:sudo:view:$action.php");
        $class = ucfirst($_GET['module']);
        $id = X3::db()->validateSQL($_GET['id']);
        $pk = X3_Module::getInstance($class)->getTable()->getPK();
        $scope = array("$pk" => $id);
        $model = X3_Module_Table::get($scope,1,$class);
        if(is_file($path)){
            $this->template->render("sudo/view/$action",array('model'=>$model,'class'=>$class));
        }else
            $this->template->render('sudo/view',array('model'=>$model,'class'=>$class));
    }
    
    public function actionEdit() {
        if(!X3::user()->superAdmin)
            throw new X3_404();
        $action = strtolower($_GET['module']);
        $path = X3::app()->getPathFromAlias("@views:admin:sudo:form:$action.php");
        $class = ucfirst($_GET['module']);
        $id = X3::db()->validateSQL($_GET['id']);
        $pk = X3_Module::getInstance($class)->getTable()->getPK();
        $scope = array("$pk" => $id);
        $model = X3_Module_Table::get($scope,1,$class);
        if(isset($_POST[$class])){
            $data = $_POST[$class];
            $model->getTable()->acquire($data);
            if($model->save())
                $this->redirect("/admin/list/module/$class.html");
        }
        if(is_file($path)){
            $this->template->render("sudo/form/$action",array('model'=>$model,'class'=>$class));
        }else
            $this->template->render('sudo/form',array('model'=>$model,'class'=>$class));
    }
    
    public function actionCreate() {
        if(!X3::user()->superAdmin)
            throw new X3_404();
        $action = strtolower($_GET['module']);
        $path = X3::app()->getPathFromAlias("@views:admin:sudo:form:$action.php");
        $class = ucfirst($_GET['module']);
        $model = new $class();
        if(isset($_POST[$class])){
            $data = $_POST[$class];
            $model->getTable()->acquire($data);
            if($model->save())
                $this->redirect("/admin/list/module/$class.html");
        }
        if(is_file($path)){
            $this->template->render("sudo/form/$action",array('model'=>$model,'class'=>$class));
        }
        $this->template->render('sudo/form',array('model'=>$model,'class'=>$class));
    }
    
    public function actionDelete() {
        if(!X3::user()->superAdmin)
            throw new X3_404();
        $class = ucfirst($_GET['module']);
        $id = X3::db()->validateSQL($_GET['id']);
        $pk = X3_Module::getInstance($class)->getTable()->getPK();
        $scope = array("$pk" => $id);
        X3_Module_Table::delete($scope,$class);
        $hash = isset($_GET['hash'])?'#'.$_GET['hash']:'';
        $this->redirect("/admin/list/module/$class.html$hash");
    }
}