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
class Admin extends X3_Module {

    public $encoding = 'UTF-8';
    
    const PAGE_LIMIT = 50;

    public function __construct($action = null) {
        //X3::app()->user->role='admin';
        parent::__construct($action);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }

    public function filter() {
        return array(
            'allow' => array(
                '*' => array('*')
            //'admin'=>array('*')
            ),
            'deny' => array(
                '*' => array('*')
            )
        );
    }
    
    public $aliases = array('menu'=>'Menu',
        'jobs'=>'Jobs','admin_menu'=>'Admin_Menu','sphere'=>'Sphere','shopsearch'=>array('Shop_Item','search'),
        'company'=>'Company','video'=>'Video','tools'=>'Admin_Tools','settings'=>'SysSettings',
        'props'=>'Shop_Properties','region'=>'Region','service'=>'Service','page'=>'Page','banner'=>'Banner','news'=>'News','article'=>'Article',
        'seo'=>'Seo','notify'=>'Notify','storage'=>'User_Storage','langs'=>'Lang','promo'=>'Promo','subscribe'=>'Subscribe','photo'=>'Photo');

    public $modules = array(
        'admin' => array(
            'Menu'=>array(
                'general'=>array('add'),
                'common'=>array('add_sub','edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Меню')
            ),
            'Subscribe'=>array(
                'general'=>array('add','manual','conf','sendnow'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Подписчик','manual'=>'Ручная рассылка','conf'=>'Настройки','sendnow'=>'Разослать сейчас!')
            ),
            'Seo'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+')
            ),
            'Notify'=>array(
                'general'=>array('add','sendclients'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+','sendclients'=>'Оповещение')
            ),
            'User_Storage'=>array(
                'general'=>array(),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array()
            ),
            'News'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Новость')
            ),
            'Promo'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Промо фото')
            ),
            'Admin_Menu'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Меню')
            ),
            'Admin_Tools'=>array(
                'general'=>array(),
                'common'=>array(),
                'direct'=>array('process','cleancache','redirects'),
                'labels'=>array()
            ),
            'Photo'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Фото')
            ),
            'UI'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ ЮИ')
            ),
            'Jobs'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Вакансия')
            ),
            'Region'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Регион')
            ),
            'Sphere'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Сферу деятельности')
            ),
            'Video'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Видео')
            ),
            'Company'=>array(
                'general'=>array('add'),
                'common'=>array('edit','excel','upload','content','autoupdate','payment'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Компания','toexcel'=>'в Excel')
            ),
            'Service'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Услуга')
            ),
            'SysSettings'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Группа')
            ),
            'Page'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Страницу')
            ),
            'Banner'=>array(
                'general'=>array('add'),
                'common'=>array('edit','delete'),
                'direct'=>array('save'),
                'labels'=>array('add'=>'+ Баннер')
            ),
            'Lang'=>array(
                'general'=>array(),
                'common'=>array(),
                'direct'=>array('save'),
                'labels'=>array()
            ),            
        )
    );
    
    
    public function UIactions(){
        return array(
            'add'=>array(),
            'add_sub'=>array(
                'html'=>'<a href="#add"><img src="/application/modules/Admin/images/buttons/add.png" /></a>',
                'click'=>'function(el){console.log(el.data)}'
            ),            
            'edit'=>array(
                'html'=>'<a href="#edit"><img src="/application/modules/Admin/images/buttons/edit.png" /></a>',
                'click'=>'function(el){console.log(el.data)}'
            ),            
            'delete'=>array(
                'html'=>'<a href="#delete"><img src="/application/modules/Admin/images/buttons/delete.png" /></a>',
                'click'=>'function(el){console.log(el.data)}'
            ),            
        );
    }

    public function cache() {
        return array(
                //'cache'=>array('actions'=>'list','trigger'=>array($this,'myCache'),'role'=>'admin','expire'=>'+1 hour'),
                //'nocache'=>array('actions'=>'list','role'=>'admin')
        );
    }

    public function myCache() {
        $test = (isset($_GET['module']));
        if ($test) {
            if ($_GET['module'] == 'order') {
                /* $till = X3::app()->db->fetch("SELECT MAX(created_at) FROM data_order");
                  $till = array_shift($till);
                  $from = $till-date("t")*86400;
                  if(isset($_GET['from'])) $from = strtotime($_GET['from']);
                  if(isset($_GET['till'])) $till = strtotime($_GET['till']);
                  X3::app()->cache->filename = "<controller>.<action>." . strtolower($_GET['module']); */
                return false;
            }else
                X3::app()->cache->filename = "<controller>.<action>." . strtolower($_GET['module']);
        }
        return $test;
    }
    
    public function saveModule($module) {
        $class = get_class($module);
        if(isset($_POST[$class]) && $module instanceof X3_Module_Table){
            $module->acquire($_POST[$class]);
            if(isset($_POST[$class][$module->table->getPK()]) && !is_null($_POST[$class][$module->table->getPK()])){
                $module->table->setIsNewRecord(FALSE);
            }
            if (strtolower($class) == 'syssettings' && isset($_POST[$class]['type'])) {
                $model->_fields['value'][0] = $_POST[$class]['type'];
            }
            foreach ($module->_fields as $l => $f){
                if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                    $module->table[$l] = (isset($_POST[$class][$l])) ? 1 : 0;
                }
                if ($f[0] == 'file' && isset($_POST[$class]["{$l}_delete"])) {
                    $file = "uploads/$class/".$_POST[$class]["{$l}_source"];
                    if(is_file($file)){
                        @unlink($file);
                        unset($_POST[$class]["{$l}_source"]);
                    }
                }
                if ($f[0] == 'file' && (isset($_FILES[$class]) || isset($_POST[$class]["{$l}_url"]))) {
                    if(isset($_FILES[$class])){
                        $handle = new Upload($module, $l);
                        $res = $handle->save();
                    }
                    if(isset($_POST[$class]["{$l}_url"]) && $_POST[$class]["{$l}_url"]!='' && isset($res) && is_bool($res)){
                        $url = $_POST[$class]["{$l}_url"];
                        $ext = pathinfo($url,PATHINFO_EXTENSION);
                        $filename = $class . "-" . time() . rand(100,999) . "." . $ext;
                        @file_put_contents(X3::app()->basePath . "/uploads/$class/$filename", file_get_contents($url));
                        $module->$l = $filename;
                    }
                }
                if((isset($f['ref']) || $l == 'parent_id') && $_POST[$class][$l]==='' && ((isset($f['default']) && ($f['default']=='NULL' || $f['default'] === null)) || in_array('null',$f))){
                    $module->table[$l] = null;
                }
            }
            if($module->save()){
                return true;
            }else 
                return false;
        }
        $module->addError('all','Непредвиденный вызов сохранения.');
        return false;
    }
    
    public function beforeAction($action) {
        if(X3::user()->isGuest() && $action=='index')
            return true;
        elseif(X3::user()->isGuest()) 
            $this->redirect('/site/error');
        if(!isset($this->aliases[$action])) return true;
        $class = $this->aliases[$action];
        $labels = array();
        $paginator = "";
        if(is_array($class)){
            $class[0] = new $class[0];
            call_user_func($class);            
        }else{
            $abc = null;
            $module = new $class();
            $actions = array();
            $ui = array();            
            $suffix = "";
            $subaction = "";
            if($module instanceOf X3_Module_Table){
                $query = $module->getDefaultScope();
                $single = false;
                $new = false;
                if(isset($this->modules[X3::app()->user->group]) && isset($this->modules[X3::app()->user->group][$class])){
                    $actions = $this->modules[X3::app()->user->group][$class];
                    $_ui = array();
                    foreach($actions as $k=>$a)
                        if($k!='labels')
                            $_ui = array_merge ($_ui,$a);
                    foreach($_ui as $a){
                        if(array_key_exists($a,$_GET)){
                            $subaction = $a;
                            if($a == 'edit'){
                               $suffix = "_form";
                               $query['@condition']["{$module->table->getPK()}"] = $_GET[$a];
                               $single = true;
                               $new = false;
                            }else
                            if($a == 'add'){
                               $suffix = "_form";
                               $new = true;
                            }else
                            if($a == 'delete'){
                                X3_Module_Table::deleteByPk($_GET[$a],$module);
                                X3_Session::writeOnce('error','Удалено');
                                $this->redirect("/admin/$action");
                            }else
                            if($a == 'save'){
                                if($this->saveModule($module)){
                                    if(IS_AJAX){
                                        echo('OK');
                                        exit;
                                    }else
                                        $this->redirect("/admin/$action");
                                }else{
                                    if(IS_AJAX){
                                        echo('ERROR');
                                        exit;
                                    }else{
                                        $suffix = "_form";
                                        X3_Session::writeOnce('error','Возникли ошибки при сохранении');
                                        $single=true;
                                        $new = true;
                                    }
                                }
                            }else
                            {
                                $suffix = "_$a";                                
                                if($_GET[$a]>0){
                                    $query['@condition']["{$module->table->getPK()}"] = $_GET[$a];
                                    $single = true;
                                }elseif($_GET[$a]!='list'){
                                    $single = false;
                                    $new = false;
                                }
                                $m = "exec".ucfirst($a);
                                if(method_exists($module, $m)){
                                    $module->$m();
                                }
                            }
                            //can't allow 2 subactions
                            break;
                        }
                    }
                    if(!$single){
                        $lim = self::PAGE_LIMIT;
                        if(property_exists($class, "pages")){
                            $lim = $class::$pages;
                        }
                        if($lim === 'ABC'){
                            $abc = X3::db()->fetchAll("SELECT DISTINCT UCASE(SUBSTRING(title,1,1)) AS `capital` FROM `manufacturer` ORDER BY title");
                            if(isset($_GET['page'])){
                                $page = (int)$_GET['page'] - 1;
                            }elseif(X3::user()->{"$action-page"}!=null){
                                $page = X3::user()->{"$action-page"};
                            }else
                                $page = 0;
                            X3::user()->{"$action-page"} = $page;
                            $query['@condition']['title']=array('LIKE'=>"'{$abc[$page]['capital']}%'");
                        }else{
                            if(isset($_GET['page'])){
                                $page = (int)$_GET['page']-1;
                            }elseif(X3::user()->{"$action-page"}!=null){
                                $page = X3::user()->{"$action-page"};
                            }else
                                $page = 0;
                            if($page<0) $page = 0;
                            X3::user()->{"$action-page"} = $page;
                            $query['@offset']=$page*$lim;
                            $query['@limit']=$lim;
                        }
                        if(isset($_GET['order'])){
                            $order = explode("@", $_GET['order']);
                            X3::user()->{"$action-sort"} = $order;
                            if(isset($module->_fields[$order[0]]['order'])){
                            $query = 
                                $module->{$module->_fields[$order[0]]['order']}($query,$order[1]);
                            }else
                                $query['@order'] = implode(' ', $order);
                        }elseif(X3::user()->{"$action-sort"}!=null){
                            $order = X3::user()->{"$action-sort"};
                            if(isset($module->_fields[$order[0]]['order'])){
                            $query = 
                                $module->{$module->_fields[$order[0]]['order']}($query,$order[1]);
                            }else
                                $query['@order'] = implode(' ', $order);
                        }
                    }
                }
                if(!$new){
                    if($action == 'shop' && $subaction=='edit' && $single)
                        unset($query['@condition']['group_id']);
                    $module = $module->formQuery($query)->asObject($single);
                    if(!$single){
                        $_q = $query;
                        unset($_q['@limit'],$_q['@offset']);
                        $eQuery = X3::db()->queryClass;
                        $eQuery = new $eQuery($module->tableName);
                        $total = $eQuery->formQuery($_q)->count();
                        $paginator = $this->pagin($total,$page,$action,$abc);
                    }
                }
                $labels = $module->fieldNames();
            }elseif($module instanceOf X3_Module){
                if(isset($this->modules[X3::app()->user->group]) && isset($this->modules[X3::app()->user->group][$class])){
                    $actions = $this->modules[X3::app()->user->group][$class];
                    $_ui = array();
                    foreach($actions as $k=>$a)
                        if($k!='labels')
                            $_ui = array_merge ($_ui,$a);
                    foreach($_ui as $a){
                        if(array_key_exists($a, $_GET)){
                            $subaction = $a;
                            $suffix = "_$a";
                            $m = "exec".ucfirst($a);
                            if(method_exists($module, $m)){
                                $module->$m();
                            }
                            //can't allow 2 subactions
                            break;
                        }
                    }
                }
            }
            $path = realpath(X3::app()->getPathFromAlias("@views:admin:templates:{$action}$suffix.php"));
            if($suffix == "" && property_exists($module,'_fields') && isset($module->_fields['parent_id']))
                $suffix = ".tree"; //generate tree-styled template for self-parented modules
            if(!$path)
                $path = realpath(X3::app()->getPathFromAlias("@views:admin:templates:default$suffix".AdminSide::$theme.".php"));
            if(IS_AJAX)
                echo $this->template->renderPartial($path,
                        array('modules'=>$module,'class'=>$class,'actions'=>$actions,'action'=>$action,'subaction'=>$subaction,'functional'=>"",'paginator'=>$paginator,'moduleTitle'=>$module->moduleTitle(),'labels'=>$labels));
            else
                $this->template->render($path,
                        array('modules'=>$module,'class'=>$class,'actions'=>$actions,'action'=>$action,'subaction'=>$subaction,'functional'=>"",'paginator'=>$paginator,'moduleTitle'=>$module->moduleTitle(),'labels'=>$labels));
        }
        $this->stopBubbling(__METHOD__);
        $action = null;
        return false;
    }

    public function actionIndex() {
        X3::app()->profile->enable = false;
        if(X3::user()->isGuest()){
            if (!isset($_GET['name'])) throw new X3_404();
            $error = false;
            $name = X3::db()->validateSQL($_GET['name']);
            if (isset($_POST['password'])) {
                $password = $_POST['password'];
                $user = new UserIdentity($name, $password);
                if ($user->login()) {
                    if(!$user->isRemembered() && isset($_POST['rememberme']))
                        $user->remember(86400 * 45); // 45 days                    
                    $url = $_SERVER['HTTP_REFERER'] == '' ? '/admin' : $_SERVER['HTTP_REFERER'];
                    $this->controller->redirect($url);
                } else {
                    $error = 'Пароль не верен.';
                }
            }
            $this->template->layout = null;
            X3::app()->profile->enable = false;
            $this->template->render('login', array('error' => $error));
        } elseif (X3::app()->user->isAdmin()) {
            $this->template->render('index');
        }else{
            X3::app()->user->logout();
            throw new X3_404();
        }
    }

    public function actionList() {
        $this->template->layout = null;
        $class = ucfirst($_GET['module']);
        $order = (isset($_GET['order'])) ? strtolower(urldecode($_GET['order'])) : 'id';
        $order = "`$order`";
        $models = new $class();
        $models = $models->table->select('*')->order($order)->asObject();
        $this->template->render(strtolower($_GET['module']), array('class' => $class, 'models' => $models));
    }

    public function actionAdd() {
        $this->template->layout = null;
        $class = ucfirst($_GET['module']);
        $path = X3::app()->getPathFromAlias("@app:modules:{$class}.php");
        if (!file_exists($path))
            exit;
        $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list." . strtolower($_GET['module']);
        if (file_exists($path))
            X3::app()->cache->flush($path);
        $model = new $class;
        $this->template->render(strtolower($class) . '_form', array('model' => $model, 'class' => $class));
    }

    public function actionEdit() {
        $this->template->layout = null;
        $id = (int) $_GET['id'];
        $class = ucfirst($_GET['module']);
        $path = X3::app()->getPathFromAlias("@app:modules:{$class}.php");
        if (!file_exists($path))
            exit;
        $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list." . strtolower($_GET['module']);
        if (file_exists($path))
            X3::app()->cache->flush($path);
        $model = new $class();
        $model = $model->table->select('*')->where("id=$id")->asObject(true);
        $this->template->render(strtolower($class) . '_form', array('model' => $model, 'class' => $class));
    }

    public function actionSave() {
        $class = ucfirst($_GET['class']);
        $path = X3::app()->getPathFromAlias("@app:modules:{$class}.php");
        if (!file_exists($path))
            exit;
        $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list." . strtolower($_GET['class']);
        if (file_exists($path))
            X3::app()->cache->flush($path);
        $model = new $class;
        $data = $_POST[$class];
        $model->table->acquire($data);
        foreach ($model->_fields as $l => $f)
            if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                $model->table[$l] = (isset($data[$l])) ? 1 : 0;
            }
        if ($model->save()) {
            echo json_encode(array('status' => 'OK'));
        } else {
            $errors = $model->table->getErrors();
            $errors['db'] = X3::app()->db->getErrors();
            echo json_encode(array('errors' => $errors));
        }
        exit;
    }

    public function actionFile() {
        $class = ucfirst($_GET['class']);
        $path = X3::app()->getPathFromAlias("@app:modules:{$class}.php");
        if (!file_exists($path))
            exit;
        $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list." . strtolower($_GET['class']);
        if (file_exists($path))
            X3::app()->cache->flush($path);
        $model = new $class;
        $data = $_POST[$class];
        $model->table->acquire($data);
        foreach ($model->_fields as $l => $f)
            if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                $model->table[$l] = (isset($data[$l])) ? 1 : 0;
            }
        if ($class == 'Syssettings' && isset($_POST['Syssettings']['type'])) {
            $model->_fields['value'][0] = $_POST['Syssettings']['type'];
        }
        foreach ($model->_fields as $name => $field) {
            if ($field[0] == 'file' && isset($_FILES[$class])) {
                $handle = new Upload($model, $name);
                $handle->save();
            }
        }
        $errors = $model->table->getErrors();
        /* if(empty($errors) && $handle->filename != NULL){
          $image = new Image();
          $image->load("uploads/$class/$handle->filename")->fitSize(174,113)->save();
          } */
        if ($model->save()) {
            echo json_encode(array('status' => 'OK'));
        } else {
            $errors = $model->table->getErrors();
            $errors['db'] = X3::app()->db->getErrors();
            echo json_encode(array('errors' => $errors));
        }
        exit;
    }

    public function actionStatus() {
        if(X3::user()->isGuest()){
            exit;
        }
        $id = X3::db()->validateSQL($_GET['pk']);
        $action = $_GET['action'];
        if(!isset($this->aliases[$action])){
            echo 'ERROR. No Alias';
            exit;
        }
        $module = $this->aliases[$action];
        if (!class_exists($module)){
            echo 'ERROR. No module';
            exit;
        }
        $pk = $_GET['pk'];
        $attr = $_GET['attr'];
        $module = X3_Module_Table::getByPk($pk,$module);
        if ($module==null || !isset($module->_fields[$attr])){
            echo 'ERROR. No record';
            exit;
        }
        $module->$attr = (int)$_GET['status'];
        if ($module->save())
            echo 'OK';
        else{
            print_r($module->table->getErrors());
            echo X3::db()->getErrors();
        }
        exit;
    }

    public function actionDelete() {
        if (X3::app()->user->isGuest())
            exit;
        $id = (int) $_POST['id'];
        if ($id > 0) {
            $module = ucfirst($_POST['class']);
            $path = X3::app()->getPathFromAlias("@app:modules:{$module}.php");
            if (!file_exists($path))
                exit;
            $path = X3::app()->cache->directory . DIRECTORY_SEPARATOR . "admin.list." . strtolower($_POST['class']);
            if (file_exists($path))
                X3::app()->cache->flush($path);
            $model = new $module();
            if (property_exists($model, 'backlink') && isset($model->backlink))
                foreach ($model->backlink as $cl => $lk) {
                    $cl = new $cl;
                    $act = array_pop($lk);
                    $lk = array_shift($lk);
                    switch ($act) {
                        case "DELETE":
                            $cl->table->delete()->where("`$lk`='$id'")->execute();
                            break;
                        case "NULL":
                            $cl->table->update(array($lk => NULL))->where("`$lk`='$id'")->execute();
                            break;
                        default:
                            break;
                    }
                }
            if ($model->table->delete()->where("id='$id'")->execute()) {
                echo "OK";
                exit;
            } else {
                echo "deletition error!!!";
                exit;
            }
        }else
            echo "", exit;
    }

    public function actionStore() {
        $shown = (bool) $_POST['shown'];
        X3::app()->user->panel = array('shown' => $shown);
    }

    public function actionInline() {
        if (!X3::app()->user->isAdmin())
            exit;
        $module = ucfirst(strtolower($_POST['module']));
        $path = X3::app()->getPathFromAlias("@app:modules:$module.php");
        if (!is_file($path)) {
            die(json_encode(array('status' => 'ERROR', 'message' => "Модуль '$module' не существует!")));
        }
        $id = (int) $_POST['id'];
        if (NULL === ($model = X3_Module_Table::getByPk($id, $module)))
            die(json_encode(array('status' => 'ERROR', 'message' => "В модуле '$module' нет записей с номером #$id!")));
        $attr = $_POST['attr'];
        if (!isset($model->_fields[$attr]))
            die(json_encode(array('status' => 'ERROR', 'message' => "В модуле '$module' нет свойства `$attr`!")));
        $text = $_POST['text'];
        $model->$attr = $text;
        if ($model->save())
            die(json_encode(array('status' => 'OK', 'message' => "Сохранено!")));
        else {
            $text = X3::app()->db->getErrors() . "<ul>";
            $errs = $model->table->getErrors();

            foreach ($errs as $err) {
                $text .= "<li>" . $err[0] . "</li>";
            }
            $text.="</ul>";
            die(json_encode(array('status' => 'ERROR', 'message' => "Обнаружены следующие ошибки:<br/>$text")));
        }
    }

    /**
     * 28.04.2012
     * it is raining...
     */
    public function actionModules() {
        $this->template->layout = null;
        if (isset($this->modules[X3::app()->user->group])){
            $modules = array_keys($this->modules[X3::app()->user->group]);
            echo json_encode($modules);
        }else
            echo '[]';
        exit;
    }

    public function ajaxModule() {
        if (!IS_SAME_AJAX || !isset($_GET['module']))
            return false;
        $module = ucfirst($_GET['module']);
        $path = X3::app()->getPathFromAlias("@app:modules:$module.php");
        if (!file_exists($path))
            return false;
        return $module;
    }

    public function actionJson() {
        if (FALSE === ($module = $this->ajaxModule()))
            exit;
        $this->template->layout = null;
        $filter = array();
        if (is_array($_POST))
            $filter = $_POST;
        $ui = array();
        if(isset($this->modules[X3::app()->user->group]) && isset($this->modules[X3::app()->user->group][$module])){
            $actions = $this->modules[X3::app()->user->group][$module];
            $_ui = $this->UIactions();
            foreach($actions as $action){
                $ui[$action] = $_ui[$action];
            }
        }
        $models = X3_Module_Table::get($filter, false, $module, true);
        echo json_encode(array('table'=>$models,'fields'=>X3_Module_Table::getInstance($module)->_fields,'ui'=>$ui));
        exit;
    }

    public function actionTemplates() {
        if (FALSE === ($module = $this->ajaxModule()) && !isset($_GET['get']))
            exit;
        $this->template->layout = null;
        if ($module != false) {
            $m = strtolower($module);
            $file = "@app:modules:Admin:admin:templates:template_{$m}.php";
            if (!file_exists(X3::app()->getPathFromAlias($file)))
                $file = "@app:modules:Admin:admin:templates:template.php";
            
            $model = X3_Module::getInstance($module);
            //Getting field names
            $fields = array_keys($model->_fields);
            //Leaving only needed attributes
            if (isset($_GET['attributes']) && is_array($_GET['attributes'])) {
                $fields = array_intersect($fields, $_GET['attributes']);
            }
            //For further use of field data types
            $infos = $model->_fields;

            $labels = array();
            $names = array();
            foreach ($fields as $name) {
                $labels[$name] = $model->fieldName($name);
                $names[$name] = '${' . $name . '}';
            }
            $actions = array();
            if(isset($this->modules[X3::app()->user->group]) && isset($this->modules[X3::app()->user->group][$module])){
                $actions = $this->modules[X3::app()->user->group][$module];
            }
            $this->template->render($file, array('module' => $module,'fields'=>$fields,'info'=>$infos,'labels'=>$labels,'names'=>$names,'actions'=>$actions));
        } else {
            $tpl = $_GET['get'];
            $file = "@app:modules:Admin:admin:templates:$tpl.php";
            if (!file_exists(X3::app()->getPathFromAlias($file)))
                $file = "@app:modules:Admin:admin:templates:template.php";
            $this->template->render($file);
        }
    }

    public function actionGetlayout() {
        $this->renderPartial('@app:modules:Admin:admin:layouts:main.php');
    }
    
    function actionGrab() {
        if(!X3::user()->isAdmin()) throw new X3_404();
        if(isset($_GET['q'])){
            echo Grabber::grep(array('find'=>'1','q'=>$_GET['q']));
            //echo file_get_contents("http://grab.instinct.kz/?find&q=".$_GET['q']);
        }elseif(isset($_GET['modelid']) && isset($_GET['hid'])){
            $group_id = (int)$_GET['hid'];
            $model_id = (int)$_GET['modelid'];
            $props = Grabber::grep(array('modelid'=>$model_id,'hid'=>$_GET['hid']));
            //$props = file_get_contents('http://grab.instinct.kz?modelid='.$model_id.'&hid='.$_GET['hid']);
            $props = json_decode($props,true);
            if($props==null)
                echo '[]';
            else{
                //check if such group already exists
                $gid = Grabber::getGroupId($group_id);
                $props['item_id'] = 0;
                if($props['articule']!=''){
                    $props['item_id'] = Shop_Item::get(array('articule'=>$props['articule']),1)->id;
                }
                $mans = Manufacturer::get();
                $props['manname'] = '';
                foreach($mans as $man){
                    if(stripos(trim($props['title']),$man->title." ")===0){
                        $props['manname'] = $man->name;
                        break;
                    }
                }
                $props['title'] = strip_tags($props['title']);
                if($gid>0){
                    $group = Shop_Group::getByPk($gid);
                    $props['group_id'] = $gid;
                    $props['group_title'] = $group->title;
                    if($group->bbracket) {
                        $props['title'] = preg_replace("/\(([^\)]+?)\)/", '', $props['title']);
                    }
                    //TODO: update and compare fetched and exist properties!
                    foreach ($props as &$prop){
                        if(!is_array($prop)) continue;
                        //See if there is new properties
                        $key = Shop_Properties::parseKey("{$prop['label']}");   
                        if(is_array($prop))
                            $prop['name'] = $key;
                        if(NULL!==($P = Shop_Properties::get(array('group_id'=>$gid,array(array('name'=>$key),array('synonymous'=>array('LIKE'=>"'%{$key};%'")))),true))){
                            $prop['label'] = $P->label;
                            $prop['name'] = $P->name;
                            if($prop['type'] == 'decimal' && $P->type == 'integer'){
                                $prop['type'] = 'decimal';
                                $P->type = 'decimal';
                            }else
                                $prop['type'] = $P->type;
                            if($P->type == 'integer'){
                                $prop['value'] = (int)($prop['value']);
                            }
                            if($P->type == 'decimal'){
                                $prop['value'] = (double)($prop['value']);
                            }
                            if($P->type == 'boolean'){
                                $prop['value'] = ($prop['value']==='да' || $prop['value']==='1' || $prop['value']===1 || $prop['value']==='есть')?1:0;
                            }
                            $prop['weight'] = $P->weight;
                            $prop['status'] = $P->status;
                            $prop['isnew']=0;
                        }
                    }
                }else{
                    $props['group_title'] = "Без имени".rand(1,100000);
                    foreach ($props as $prop)
                        if(!$found && mb_strpos(mb_strtolower($prop['label'],'UTF-8'), 'тип',0,'UTF-8')!==false){
                            $props['group_title'] = $prop['value'];
                            break;
                        }
                }
                try{
                    //if(X3::mongo()->query(array("shop_item:count"=>array('modelid'=>$model_id)))===0)
                        //X3::mongo()->query(array("shop_item:insert"=>$props));
                }catch(Exception $e){
                    X3::log($e->getMessage(),'kansha_error');
                }
                echo json_encode($props);
            }
            //echo file_get_contents("http://localhost:8111/?modelid=".$_GET['modelid']."hid=".$_GET['hid']);
        }
        exit;
    }
    

    public function actionShopgroupsave() {
        $class = 'Shop_Group';
        if(isset($_POST[$class])){
        $data = $_POST[$class];
        if(isset($data['id'])){
            $model = Shop_Group::getByPk($data['id']);
            if($model == NULL)
                throw new X3_Exception('Сохранение модели #'.$data['id']);
        }else
            $model = new $class;
        $model->table->acquire($data);
        $model->category_id = $data['category_id'];
        foreach ($model->_fields as $l => $f)
            if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                $model->table[$l] = (isset($data[$l])) ? 1 : 0;
            }
        if ($model->save()) {
            if(isset($_POST['regenerate'])){
                exec('php run.php shop regentitle group_id='.$model->id.' > /dev/null 2>&1 &');
                //system('php run.php shop regentitle group_id='.$model->id.'');
            }
            $this->redirect('/admin/shopgroup/edit/'.$model->id);
            //$this->redirect('/admin/shopgroup');
            echo json_encode(array('status' => 'OK'));
        } else {
            $errors = $model->table->getErrors();
            $errors['db'] = X3::app()->db->getErrors();
            die(json_encode(array('errors' => $errors)));
        }
        }
        //if((isset($_POST['take']) && $_POST['take']=='stay') || (isset($_POST['take2']) && $_POST['take2']=='stay'))
            //$this->redirect('/admin/shopgroup');
        //else
            $this->redirect('/admin/shopgroup/edit/'.$model->id);
    }

    public function actionShopcategorysave() {
        $class = 'Shop_Category';
        $model = new $class;
        $data = $_POST[$class];
        $model->table->acquire($data);
        if(isset($data['id'])){
            $model->table->setIsNewRecord(false);
        }
        foreach ($model->_fields as $l => $f)
            if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                $model->table[$l] = (isset($data[$l])) ? 1 : 0;
            }
        foreach ($model->_fields as $name => $field) {
            if ($field[0] == 'file' && isset($_FILES[$class])) {
                $handle = new Upload($model, $name);
                $handle->save();
            }
        }
        //$errors = $model->table->getErrors();
        /* if(empty($errors) && $handle->filename != NULL){
          $image = new Image();
          $image->load("uploads/$class/$handle->filename")->fitSize(174,113)->save();
          } */
        if ($model->save()) {
            $this->redirect('/admin/shopcategory');
            echo json_encode(array('status' => 'OK'));
        } else {
            $errors = $model->table->getErrors();
            $errors['db'] = X3::app()->db->getErrors();
            die(json_encode(array('errors' => $errors)));
        }
        $this->redirect('/admin/shopcategory');
    }
    
    public function actionSavecompany() {
        $class = 'Company';
        $data = $_POST['Company'];
        $model = new Company();
        $model->table->acquire($data);
        if(isset($data['id'])){
            $model->table->setIsNewRecord(false);
        }
        foreach ($model->_fields as $l => $f)
            if ($f[0] == 'boolean' || $f[0] == 'tinyint[1]') {
                $model->table[$l] = (isset($data[$l])) ? 1 : 0;
            }
        foreach ($model->_fields as $name => $field) {
            if ($field[0] == 'file' && isset($_FILES[$class])) {
                $handle = new Upload($model, $name);
                $handle->save();
            }
        }
        if(!$model->save()){
            var_dump($model->table->getErrors());
            exit;
        }
        if(isset($_POST['Company_Service'])){
            $serv = $_POST['Company_Service'];
            if(NULL === ($s = Company_Service::get(array('company_id'=>$model->id),1)))
                $s = new Company_Service;
            $s->company_id = $model->id;
            $s->services = isset($serv['services'])?json_encode($serv['services']):array();
            $s->groups = isset($serv['groups'])?json_encode($serv['groups']):array();
            $s->save();
        }else{
            $s = Company_Service::get(array('company_id'=>$model->id),1);
            if($s!=NULL){
                $s->services = array();
                $s->groups = array();
                $s->save();
            }
        }
        $array = array("Monday"=>"1","Tuesday"=>"1","Wednesday"=>"1","Thursday"=>"1","Friday"=>"1","Saturday"=>"1","Sunday"=>"1");
        foreach($array as $k=>$a){
            if(is_null($_POST['Address'][0]['worktime']['days'][$k]))
                $_POST['Address'][0]['worktime']['days'][$k] = $a;
        }        
        $ads = $_POST['Address'];
        foreach ($ads as $key => $address) {
            $a = new Address();
            $a->table->acquire($address);
            if($address['id']>0){
                $a->table->setIsNewRecord(false);
            }
            $a->worktime = $address['worktime'];
            $a->phones = $address['phones'];
            if($key==0)
                $a->ismain = 1;
            $a->weight = $key;
            $a->company_id = $model->id;
            if(!$a->save()){
                var_dump($a->table->getErrors());
                exit;
            }
        }
        $this->redirect('/admin/company/edit/'.$model->id);
    }
       
    
    public function pagin($total,$page,$action,$abc) {
        if($abc == null)
            $total = ceil($total / self::PAGE_LIMIT);
        else
            $total = count($abc);
        if($total==1) return '';
        $url = '/admin/'.$action.'/';
        foreach($_GET as $k=>$g)
        if($k!='page')
            $url.=$k.'/'.$g;
        $url = trim($url,'/');
        $start = $page-10;
        $end = $page+10;
        if($start<2)$start=2;
        if($end>$total)$end=$total;
        $html = "Страницы: ";
        if($page == 0)
            $html .= ($abc==null?'<span>1</span>':"<span>{$abc[0]['capital']}</span>");
        else
            $html .= ($abc==null?'<a href="/'.$url.'/page/1">1</a>':"<a href=\"/$url/page/1\">{$abc[0]['capital']}</a>");
        if($page>10)
            $html .= '<span>...</span>';
        for($i=$start;$i<$end;$i++){
            if($page == $i-1)
                $html .= '<span>'.($abc==null?$i:$abc[$i-1]['capital']).'</span>';
            else
                $html .= '<a href="/'.$url.'/page/'.$i.'">'.($abc==null?$i:$abc[$i-1]['capital']).'</a>';
        }
        if($page<$total-10)
            $html .= '<span>...</span>';
        if($page == $total-1)
            $html .= '<span>'.($abc==null?$i:$abc[$i-1]['capital']).'</span>';
        else
            $html .= '<a href="/'.$url.'/page/'.$total.'">'.($abc==null?$total:$abc[$total-1]['capital']).'</a>';
        
        return $html;
    }
    
}

?>
