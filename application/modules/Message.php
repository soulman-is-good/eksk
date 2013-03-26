<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Default
 *
 * @author Soul_man
 */
class Message extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'data_message';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_to' => array('integer[10]', 'unsigned', 'index', 'ref' => array('User', 'id', 'default' => 'email')),
        'user_from' => array('integer[10]', 'unsigned', 'index', 'ref' => array('User', 'id', 'default' => 'email')),
        'content' => array('content'),
        'status' => array('boolean', 'default' => '0'),
        'hidden_id' => array('integer[10]','unsigned', 'default' => '0'),
        'created_at' => array('datetime', 'default' => '0'),
    );

    public function fieldNames() {
        return array(
            'created_at' => 'Дата отправки',
            'user_from' => 'От кого',
            'user_to' => X3::translate('Кому'),
            'content' => X3::translate('Сообщение'),
            'status' => 'Прочитанное',
        );
    }

    public function filter() {
        return array(
            'allow' => array(
                'user' => array('index', 'show', 'send', 'file','with','read','count','deleteall','delete'),
                'ksk' => array('index', 'show', 'send', 'file','with','read','count','delete','deleteall'),
                'admin' => array('index', 'show', 'send', 'file','with','read','count','delete','deleteall')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }

    public static function getUserList() {
        if(X3::user()->isAdmin()){
            $uq = X3::db()->query("SELECT id, CONCAT(name,' ',surname) AS username, role FROM data_user WHERE status>0 AND id<>".X3::user()->id);
        }else{
            $uq = X3::db()->query("SELECT id, CONCAT(name,' ',surname) AS username, role FROM data_user WHERE status>0 AND (role='user' OR role='ksk') AND id IN 
                (SELECT a1.user_id FROM user_address a1, user_address a2 
WHERE a2.user_id=".X3::user()->id." AND a1.user_id<>a2.user_id AND `a2`.`city_id` = a1.city_id AND `a2`.`region_id` = a1.region_id AND `a2`.`house` = a1.house)");
        }
        $users = array();
        while($u = mysql_fetch_assoc($uq))
            $users[$u['id']] = $u['role']=='admin'?X3::translate('Администратор').'#'.$u['id']:$u['username'];
        return $users;
    }

    public function actionIndex() {
        $id = X3::user()->id;
        $q = "FROM data_message m, data_user u WHERE ((m.user_from=$id AND m.user_to=u.id) OR (m.user_to=$id AND m.user_from=u.id)) AND m.hidden_id<>$id GROUP BY u.id";
        $count = X3::db()->count("SELECT MAX(m.created_at) latest ".$q);
        $paginator = new Paginator(__CLASS__, $count);
        $q = "SELECT u.id, CONCAT(u.name,' ',u.surname) name,u.image, u.role, MAX(m.created_at) latest " . $q . " ORDER BY latest DESC LIMIT $paginator->offset,$paginator->limit";
        $models = X3::db()->query($q);
        $this->template->render('index', array('models' => $models, 'count' => $count, 'paginator' => $paginator));
    }
    
    public function actionWith() {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $id = (int) $_GET['id'];
            $query = array(array('user_from' => $id,'user_to'=>X3::user()->id),array('user_to' => $id,'user_from'=>X3::user()->id));
            $query = array('@condition' => $query, '@order' => 'created_at DESC');
            $count = self::num_rows($query);
            //'Search the page' logic
            if(X3::user()->search!=null && X3::user()->search['type'] == 'message' && strpos($_SERVER['HTTP_REFERER'],'/search')){
                $paginator = new Paginator(__CLASS__, $count);
                $queryZ = $query;
                $queryZ['@select'] = 'content';
                $q = new X3_MySQL_Query('data_message');
                $a = X3::db()->query($q->formQuery($queryZ)->buildSQL());
                if(!is_resource($a))
                    throw new X3_Exception(X3::db()->getErrors().X3::db()->lastQuery());
                $i = 0;
                $p = 0;
                $f=false;
                while(list($data) = mysql_fetch_row($a)){
                    $p = (int)floor($i++/$paginator->limit);
                    if(($f=mb_stripos($data,X3::user()->search['word'],null,'UTF-8'))!==false)
                        break;
                }
                if($f!==false){
                    X3::user()->MessagePage = $p;
                    $paginator->page = $p;
                    $paginator->offset = $p * $paginator->limit;
                }
            }else
                $paginator = new Paginator(__CLASS__, $count);
            $query['@limit'] = $paginator->limit;
            $query['@offset'] = $paginator->offset;
            $models = self::get($query);
            $users = array();
            $uq = X3::db()->query("SELECT id,CONCAT(name,' ',surname) name, image FROM data_user WHERE id=$id OR id=".X3::user()->id);
            while($u = mysql_fetch_assoc($uq)){
                if($u['image']=='' || $u['image']==null || !is_file('uploads/User/'.$u['image']))
                    $image = '/images/default.png';
                else
                    $image = '/uploads/User/100x100/'.$u['image'];
                $users[$u['id']] = array('title'=>$u['name'],'avatar'=>$image);
            }
            $this->template->render('show', array('models' => $models, 'count' => $count, 'paginator' => $paginator,'users'=>$users,'with'=>$id));
        }else
            throw new X3_404();
    }
    
    public function actionCount() {
        //if(!IS_AJAX) throw new X3_404();
        $id = X3::user()->id;
        $msg = Message::num_rows(array('status'=>'0','user_to'=>$id));        
        $date = X3::db()->fetch("SELECT created_at FROM data_user WHERE id=$id");
        $type = X3::user()->isUser()?"(f.type='user' OR f.type='*')":(X3::user()->isKsk()?"(f.type='ksk' OR f.type='*')":"(f.type='admin' OR f.type='*')");
        $q = "SELECT f.id FROM data_warning f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE
            f.end_at>{$date['created_at']} AND f.user_id<>$id AND (
            (f.city_id IS NULL AND $type AND u.role='admin' AND
                (
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL) OR 
                    (f.city_id=a.city_id AND f.region_id IS NULL) OR
                    (f.city_id IS NULL)
                )
            )
                OR
            ($type AND u.role='ksk' AND
             (
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
             )
             )
            ) AND f.id NOT IN (SELECT warning_id FROM warning_stat WHERE user_id=$id) GROUP BY f.id
             ";
        $ncount = X3::db()->count($q);
        if(X3::user()->isAdmin())
            $G = "f.id IN (SELECT forum_id FROM forum_users fu WHERE user_id=$id AND fu.updated_at<f.updated_at) OR f.id NOT IN (SELECT forum_id FROM forum_users fu WHERE user_id=$id)";
        else
            $G = "
                (f.id NOT IN (SELECT forum_id FROM forum_users fu WHERE user_id=$id AND fu.updated_at>f.updated_at)) AND (
                f.status AND f.user_id<>$id AND (
                (
                    u.role='admin' AND 
                    (
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL) OR 
                       (f.city_id=a.city_id AND f.region_id IS NULL) OR
                       (f.city_id IS NULL)
                    )                
                )
                    OR
                (
                    u.role='ksk' AND
                    (
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                        (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                        (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                    )
                )
                )
            )";
        $q = "FROM data_forum f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE $G";
        //echo(htmlspecialchars("SELECT f.id, MAX(f.created_at) latest ".$q. " GROUP BY f.id"));exit;
        $fcount = X3::db()->count("SELECT f.id ".$q. " GROUP BY f.id");
        if(X3::user()->isAdmin())
            $q = "SELECT f.id FROM user_report f WHERE f.id NOT IN (SELECT report_id FROM user_report_stat rs WHERE rs.user_id=$id)";
        elseif(X3::user()->isUser())
            $q = "SELECT f.id FROM user_report f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE f.id NOT IN (SELECT report_id FROM user_report_stat rs WHERE rs.user_id=$id)
                AND f.status AND (
                  (
                    u.role='admin' AND 
                    (
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                       (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL) OR 
                       (f.city_id=a.city_id AND f.region_id IS NULL) OR
                       (f.city_id IS NULL)
                    )                
                  )
                    OR
                  (
                    u.role='ksk' AND
                    (
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                        (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                        (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                    )
                  )
                ) GROUP BY f.id";
        else
            $q = "SELECT f.id FROM user_report f WHERE 0";
        $rcount = X3::db()->count($q);
//        echo json_encode(array('message'=>(int)$msg,'notify'=>(int)$ncount,'themes'=>(int)0,'stats'=>(int)$rcount));
        echo json_encode(array('message'=>(int)$msg,'notify'=>(int)$ncount,'themes'=>(int)$fcount,'stats'=>(int)$rcount));
        exit;
    }
    
    public function actionRead() {
        if(!IS_AJAX) throw new X3_404();
        $id = (int)$_GET['id'];
        Message::update(array('status'=>'1'),array('user_to'=>X3::user()->id,'id'=>$id));
        exit;
    }

    public function actionFile() {
        if (isset($_FILES['file'])) {
            $h = new Upload('file');
            if($h->message!='')
                die(json_encode(array('status' => 'error', 'message' =>$h->message)));
            $orig = $h->filename;
            $ext = strtolower(pathinfo($orig,PATHINFO_EXTENSION));
            $allowed = SysSettings::getValue('Message_Uploads.Extensions', 'string', 'Разрешенные к загрузке расширения файлов', '[INVISIBLE]', 'jpg,png,gif,tif,rar,zip,doc,docx,xls,xlsx,txt,ppt,pptx');
            $allowed = array_map(function($item){return trim($item);},explode(',',$allowed));
            if(!in_array($ext,$allowed))
                die(json_encode(array('status' => 'error', 'message' => $ext.". ".strtr(X3::translate('Возможно загрузить только файлы с расширениями: {files}'),array('{files}'=>implode(', ',$allowed))))));
            $filename = md5_file($h->tmp_name);
            if ($h->saveAs($filename)) {
                if (NULL === Uploads::getByPk($filename)) {
                    $model = new Uploads();
                    $model->id = $filename;
                    $model->name = $orig;
                    $model->created_at = time();
                    $model->save();
                }
                echo json_encode(array('status' => 'ok', 'message' => array('id'=>$filename,'filename'=>$orig)));
            } else {
                echo json_encode(array('status' => 'error', 'message' => $h->message));
            }
        }else
            echo json_encode(array('status' => 'error', 'message' => 'Не выбрано файлов'));
        exit;
    }

    public function actionSend() {
        if (!X3::user()->id>0 || !IS_AJAX)
            throw new X3_404();
        if (isset($_POST['Message'])) {
            $message = $_POST['Message'];
            $user_tos = $message['user_to'];
            if (!is_array($user_tos)) {
                $user_tos = array($user_tos);
            }
            $files = explode(',',$_POST['files']);
            foreach ($user_tos as $user_to) {
                $mes = new self;
                $mes->user_to = $user_to;
                $mes->user_from = X3::user()->id;
                $mes->content = trim(preg_replace("/[\r\n]+/","\r\n",$message['content']));
                $mes->created_at = time();
                if($mes->save()){
                    foreach($files as $file){
                        $file = trim($file);
                        if($file == '') continue;
                        $F = new Message_Uploads();
                        $F->file_id = $file;
                        $F->message_id = $mes->id;
                        $F->created_at = time();
                        $F->save();
                    }
                    $userto = X3::db()->fetch("SELECT u.email FROM data_user u WHERE id='$user_to'");
                    $userset = X3::db()->fetch("SELECT * FROM user_settings us WHERE user_id='$user_to'");
                    if($userset['mailMessages'])
                        Notify::sendMail('NewMessage',array('name'=>X3::user()->fullname,'message'=>nl2br($mes->content)),$userto['email']);
                    echo json_encode (array('status'=>'error','message'=>X3::translate('Сообщение успешно отправлено')));
                }else{
                    $errors = $mes->getTable()->getErrors();
                    $html = array();
                    foreach($errors as $err){
                        $html []= $err[0];
                    }
                    echo json_encode (array('status'=>'error','message'=>implode('<br />',$html)));
                }
            }
        }else
            echo json_encode (array('status'=>'error','message'=>X3::translate('Ошибка при заполнении формы')));
        exit;
    }
    
    public function actionDelete(){
        if(isset($_GET['id']) && ($id = (int)$_GET['id'])>0){
            $msg = Message::get(array(array(array('user_to'=>X3::user()->id),array('user_from'=>X3::user()->id)),'id'=>$id),1);
            if($msg != null && $msg->hidden_id>0 && $msg->hidden_id != X3::user()->id){
                Message::deleteByPk($id);
                if(IS_AJAX)
                    die('OK');
            }else if($msg != null && $msg->hidden_id != X3::user()->id){
                $msg->hidden_id = X3::user()->id;
                if($msg->save() && IS_AJAX){
                    die('OK');
                }
            }
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if(IS_AJAX)
            exit;
        throw new X3_404();
    }
    
    public function actionDeleteall(){
        if(isset($_GET['id']) && ($id = (int)$_GET['id'])>0){
            $uid = X3::user()->id;
            $q = new X3_MySQL_Query('data_message');
            Message::delete(array('hidden_id'=>array('@@'=>"hidden_id>0 AND hidden_id<>$uid"),array(array('user_from'=>$uid,'user_to'=>$id),array('user_to'=>$uid,'user_from'=>$id))));
            Message::update(array('hidden_id'=>$uid),array(array('user_from'=>$uid,'user_to'=>$id),array('user_to'=>$uid,'user_from'=>$id)));
            if(IS_AJAX)
                die('OK');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if(IS_AJAX)
            exit;
        throw new X3_404();
    }
    
    public function beforeValidate() {
        if($this->created_at == 0)
            $this->created_at = time();
    }
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            if($model){   
                Message_Uploads::delete(array('message_id'=>$model->id));
            }
        }
        parent::onDelete($tables, $condition);
    }
    
    public static function search($word) {
        $id = X3::user()->id;
        $scope = array(
            '@select'=>'m.content,u.id,u.name,u.surname,u.kskname,u.ksksurname,u.image',
            '@from'=>array('data_message'=>'m', 'data_user'=>'u'),
            '@condition'=>array(
                'm.hidden_id'=>array('<>'=>$id),
                'm.content'=>array('LIKE'=>"'%$word%'"),
                array(
                    array('m.user_from'=>$id,'m.user_to'=>array(' ='=>'u.id')),
                    array('m.user_to'=>$id,'m.user_from'=>array(' ='=>'u.id')),
                )
            ),//((m.user_from=$id AND m.user_to=u.id) OR (m.user_to=$id AND m.user_from=u.id)) AND m.hidden_id<>$id
            '@order'=>'m.created_at DESC',
            '@group'=>'u.id'
        );
        return $scope;
    }
}

?>