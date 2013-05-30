<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class Forum extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'data_forum';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'user_id'=>array('integer[10]','unsigned','index','ref'=>array('User','id','default'=>"CONCAT(name,' ',surname)")),
        'city_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('City','id','default'=>'title')),
        'region_id' => array('integer[10]', 'unsigned','default'=>'NULL', 'index', 'ref'=>array('City_Region','id','default'=>'title')),
        'house'=>array('string[10]','default'=>"NULL"),
        'flat'=>array('string[10]','default'=>"NULL"),
        'title'=>array('string[512]'),
        'status'=>array('boolean','default'=>'0'),
        'created_at'=>array('datetime'),
        'updated_at'=>array('datetime','default'=>'0')
    );

    public function fieldNames() {
        return array(
            'user_id'=>'Автор',
            'city_id'=>X3::translate('Регион'),
            'region_id'=>X3::translate('Улица'),
            'house' => X3::translate('№ дома'),
            'flat' => X3::translate('№ квартиры'),
            'title'=>X3::translate('Название темы'),
            'status'=>'Пуликован',
        );
    }
    
    public function __construct($action = null) {
        if(X3::user()->isKsk())
            $this->_fields['city_id']['ref']['query'] = array(
                '@join'=>"INNER JOIN user_address a ON a.city_id=data_city.id",
                '@condition'=>array('a.user_id'=>X3::user()->id)
                );
        parent::__construct($action);
    }    
    
    public function filter() {
        return array(
            'allow' => array(
                'user' => array('index', 'show', 'send', 'file','with','read','count'),
                'ksk' => array('index', 'show', 'send', 'file','with','read','count','create','flats','public','delete'),
                'admin' => array('index', 'show', 'send', 'file','with','read','count','create','flats','public','delete')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }
    
    public static function isMyTheme($id) {
        if(X3::user()->isAdmin())
            return true;
        $uid = X3::user()->id;
        $q = "SELECT f.id, f.title FROM data_forum f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$uid WHERE 
            MD5(CONCAT(f.title,f.id))='$id' AND ((
                    f.status AND 
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
                (f.status AND u.role='ksk' AND
                 (
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                    (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                    (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                 )
                 )) LIMIT 1";
//        return X3::db()->count($q)>0;
        return X3::db()->call("isMyForum",$id,$uid)>0;
    }


    public function actionIndex() {
        $id = X3::user()->id;
//        $type = X3::user()->isUser()?"(f.type='user' OR f.type='*')":(X3::user()->isKsk()?"(f.type='ksk' OR f.type='*')":"(f.type='admin' OR f.type='*')");
        if(X3::user()->isAdmin()){
            $q = "FROM data_forum f WHERE 1";
        }else
            $q = "FROM data_forum f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE
                (f.user_id=$id)
                    OR
                (
                    f.status AND 
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
                (f.status AND u.role='ksk' AND
                 (
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                    (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                    (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                 )
                 )
                 ";
        $count = X3::db()->count("SELECT f.id, MAX(f.created_at) latest ".$q. " GROUP BY f.id");
        $paginator = new Paginator(__CLASS__."Index", $count);
        $ordr = array('date'=>'latest','count'=>'(SELECT COUNT(0) FROM forum_message WHERE forum_id=f.id)');
        $ordrdir = array('desc','asc');
        if(X3::user()->ForumOrder==null)
            X3::user()->ForumOrder = array(
                'order'=>'date',
                'dir'=>'0'
            );
        if(isset($_GET['order'])){
            $o = explode('-',$_GET['order']);
            if(isset($ordr[$o[0]],$ordrdir[$o[1]])){
                $ord = X3::user()->ForumOrder;
                $ord['order'] = $o[0];
                $ord['dir'] = (string)$o[1];
                X3::user()->ForumOrder = $ord;
            }
        }
        $o = X3::user()->ForumOrder;
        $Oq = $ordr[$o['order']]." ".strtoupper($ordrdir[$o['dir']]);
        $q = "SELECT f.id, f.title, f.user_id, MAX(f.updated_at) latest, f.status " . $q . " GROUP BY f.id ORDER BY $Oq LIMIT $paginator->offset,$paginator->limit";
        $models = X3::db()->query($q);
        $this->template->render('index', array('models' => $models, 'count' => $count, 'paginator' => $paginator));
    }
    
    public function actionShow() {
        if (isset($_GET['id']) && (($id = $_GET['id'])) && (X3::user()->isAdmin() || Forum::isMyTheme($id))) {
            $theme = Forum::get(array('id'=>array('@@'=>"MD5(CONCAT(title,id))='$id'")),1);
            if($theme == null)
                throw new X3_404();
            $query = array('@condition' => array('forum_id'=>$theme->id), '@order' => 'created_at ASC');
            $count = Forum_Message::num_rows($query);
            //'Search the page' logic
            $goto = 0;
            if(X3::user()->search!=null && X3::user()->search['type'] == 'themes' && strpos($_SERVER['HTTP_REFERER'],'/search')){
                $paginator = new Paginator(__CLASS__."#$theme->id", $count);
                $queryZ = $query;
                $queryZ['@select'] = 'id, content';
                $q = new X3_MySQL_Query('forum_message');
                $a = X3::db()->query($q->formQuery($queryZ)->buildSQL());
                if(!is_resource($a))
                    throw new X3_Exception(X3::db()->getErrors().X3::db()->lastQuery());
                $i = 0;
                $p = 0;
                $f=false;
                while(list($mid,$data) = mysql_fetch_row($a)){
                    $p = (int)floor($i++/$paginator->limit);
                    if(($f=mb_stripos($data,X3::user()->search['word'],null,'UTF-8'))!==false){
                        $goto = $mid;
                        break;
                    }
                }
                if($f!==false){
                    $t = "Forum#{$theme->id}Page";
                    X3::user()->$t = $p;
                    $paginator->page = $p;
                    $paginator->offset = $p * $paginator->limit;
                }
            }else
                $paginator = new Paginator(__CLASS__."#$theme->id", $count);
            $query['@limit'] = $paginator->limit;
            $query['@offset'] = $paginator->offset;
            $models = Forum_Message::get($query);
            $users = array();
            $uq = X3::db()->query("SELECT id,CONCAT(name,' ',surname) name, image, role FROM data_user WHERE id IN (
                SELECT user_id FROM forum_message fm WHERE forum_id='$theme->id')");
            while($u = mysql_fetch_assoc($uq)){
                if($u['image']=='' || $u['image']==null || !is_file('uploads/User/'.$u['image']))
                    $image = '/images/default.png';
                else
                    $image = '/uploads/User/100x100/'.$u['image'];
                $users[$u['id']] = array('title'=>$u['role']=='admin'?X3::translate('Администратор').' #'.$u['id']:$u['name'],'avatar'=>$image);
            }
            $fu = Forum_Users::get(array('forum_id'=>$theme->id,'user_id'=>X3::user()->id),1);
            if($fu == null){
                $fu = new Forum_Users;
                $fu->user_id = X3::user()->id;
                $fu->forum_id = $theme->id;
            }
            $fu->save();
                //Forum_Users::update(array('updated_at'=>time()), array('forum_id'=>$theme->id,'user_id'=>X3::user()->id));
            $this->template->render('show', array('models' => $models,'theme'=>$theme, 'count' => $count, 'paginator' => $paginator,'users'=>$users,'with'=>$theme->id,'goto'=>$goto));
        }else
            throw new X3_404();
    }
    
    public function actionCreate() {
        $id = X3::user()->id;
        if($_POST['file_trigger'] == '1'){
            $this->file();
            exit;
        }
        if(isset($_GET['id']) && (int)$_GET['id']>0){
            $model = Forum::getByPk((int)$_GET['id']);
            $message = Forum_Message::get(array(
                        '@condition'=>array('forum_id'=>(int)$_GET['id'],'user_id'=>$id),
                        '@order'=>'created_at ASC'
                    ),1);
            if($message == null)
                $message = new Forum_Message();
        }else{
            $model = new Forum();
            $message = new Forum_Message();
        }
        if(isset($_POST['Forum'])){
            $data = $_POST['Forum'];
            $msg = $_POST['Message'];
            $model->getTable()->acquire($data);
            if(isset($_POST['public']))
                $model->status = '1';
            else
                $model->status = '0';
            $model->user_id = $id;
            if($model->validate()){
                $pass = false;
                if(trim($msg['content'])!=''){
                    if(!$message->getTable()->getIsNewRecord())
                        $message->id = $msg['id'];
                    $message->forum_id = 1;
                    $message->user_id = X3::user()->id;
                    $message->user_to = NULL;
                    $message->content = $msg['content'];
                    if($message->validate()){
                        if(!$model->save())
                            throw new X3_Exception(X3_Html::errorSummary($model),500);
                        $message->forum_id = $model->id;
                        if(!$message->save())
                            throw new X3_Exception(X3_Html::errorSummary($message),500);
                        $files = explode(',',$msg['files']);
                        $FS = array();
                        foreach($files as $i=>$file){
                            $file = trim($file);
                            if($file == '') {
                                unset($files[$i]);
                                continue;
                            }
                            if(NULL == ($F = Forum_Uploads::get(array('file_id'=>$file,'message_id'=>$message->id),1)))
                                $F = new Forum_Uploads();
                            $F->file_id = $file;
                            $F->message_id = $message->id;
                            $F->created_at = time();
                            if(!$F->save()){
                                echo X3::db()->getErrors().'('.X3::db()->lastQuery().')';
                                throw new X3_Exception(X3_Html::errorSummary($F),500);
                            }
                        }
                        if(empty($files))
                            $files[]='0';
                        $fs = "'".implode("','",$files)."'";
                        Forum_Uploads::delete(array('file_id'=>array('NOT IN'=>"($fs)"),'message_id'=>$message->id));
                        $pass = true;
                    }
                }else 
                    $model->save();
                if(isset($_POST['public'])){
                    $this->notify($model);
                    $fu = Forum_Users::get(array('forum_id'=>$model->id,'user_id'=>X3::user()->id),1);
                    if($fu == null){
                        $fu = new Forum_Users;
                        $fu->user_id = X3::user()->id;
                        $fu->forum_id = $model->id;
                    }
                    $fu->save();
                }
                if($pass)
                    $this->redirect('/forum/');
            }
        }
        $this->template->render('form', array('model' => $model,'message'=>$message));
    }
    
    public function actionRead() {
        if(!IS_AJAX) throw new X3_404();
        $id = (int)$_GET['id'];
        Forum_Message::update(array('status'=>'1'),array('user_to'=>X3::user()->id,'id'=>$id));
        exit;
    }
    
    public function actionDelete() {
        if(isset($_GET['id'])){
            $id = (int)$_GET['id'];
            if(X3::user()->isAdmin())
                $q = array('id'=>$id);
            else
                $q = array('user_id'=>X3::user()->id,'id'=>$id);
            if(Forum::get($q,1)!=null)
                Forum::deleteByPk($id);
            $this->redirect('/forum/');
        }
        if(isset($_GET['message'])){
            $id = (int)$_GET['message'];
            if(X3::user()->isAdmin())
                $q = array('id'=>$id);
            else
                $q = array('user_id'=>X3::user()->id,'id'=>$id);
            $msg = Forum_Message::get($q,1);
            if($msg!=null){
                Forum_Message::deleteByPk($id);
            }
            $this->redirect('/forum/'.$msg->forum_id.'.html');
        }
        throw new X3_404();
    }

    public function file() {
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
            $user_to = (int)$message['user_to'];
            $files = explode(',',$_POST['files']);
            $mes = new Forum_Message;
            $mes->user_to = $user_to>0?$user_to:NULL;
            $mes->user_id = X3::user()->id;            
            $mes->forum_id = $message['forum_id'];
            $mes->parent_id = $message['parent_id']>0?$message['parent_id']:null;
            //TODO: check $message['forum_id'] on MyTheme
            $mes->content = trim(preg_replace("/[\r\n]+/","\r\n",$message['content']));
            $mes->created_at = time();
            if($mes->save()){
                foreach($files as $file){
                    $file = trim($file);
                    if($file == '') continue;
                    $F = new Forum_Uploads();
                    $F->file_id = $file;
                    $F->message_id = $mes->id;
                    $F->created_at = time();
                    $F->save();
                }
                //$userto = X3::db()->fetch("SELECT email FROM data_user WHERE id IN ()");
                //Notify::sendMail('NewMessage',array('name'=>X3::user()->fullname,'message'=>nl2br($mes->content)),$userto['email']);
                Forum::update(array('updated_at'=>time()), array('id'=>$mes->forum_id));
                //Forum_Users::update(array('updated_at'=>time()), array('forum_id'=>$mes->forum_id,'user_id'=>X3::user()->id));
                if($mes->user_to>0 && (FALSE == ($userset = X3::db()->fetch("SELECT * FROM user_settings WHERE user_id='$mes->user_to'")) || $userset['mailForum']==1)){
                    $forum = Forum::getByPk($mes->forum_id);
                    $from = User::getByPk(X3::user()->id);
                    $to = X3::db()->fetch("SELECT id, CONCAT(name,' ',surname) username, email,phone FROM data_user WHERE id=$mes->user_to");
                    $userset = X3::db()->fetch("SELECT mailForum, smsForum FROM user_settings WHERE user_id=$mes->user_to");
                    if($userset['mailForum'])
                        $msg = Notify::sendMail('forumAnswer', array('name'=>$to['username'],'from'=>$from->getFullname(),'time'=>date('H:i',$mes->created_at).', '.I18n::date($mes->created_at,'ru'),'text'=>nl2br($mes->content),
                            'forum'=>$forum->title,'link'=>X3::app()->baseUrl . '/forum/'.$forum->id.'.html'), $to['email']);
                    if($userset['smsForum'])
                        Notify::sendSms('forumAnswer',$to['phone'], array('name'=>$from->getFullname(),'forum'=>$forum->title));
                }
                echo json_encode (array('status'=>'ok','message'=>X3::translate('Сообщение успешно отправлено')));
            }else{
                $errors = $mes->getTable()->getErrors();
                $html = array();
                foreach($errors as $err){
                    $html []= $err[0];
                }
                echo json_encode (array('status'=>'error','message'=>implode('<br />',$html)));
            }
        }else
            echo json_encode (array('status'=>'error','message'=>X3::translate('Ошибка при заполнении формы')));
        exit;
    }
    
    public function actionPublic() {
        if (isset($_GET['id']) && ($id = (int)$_GET['id'])>0) {
            if(X3::user()->isAdmin())
                Forum::update(array('status'=>'1'),array('id'=>$id));
            else
                Forum::update(array('status'=>'1'),array('user_id'=>X3::user()->id,'id'=>$id));
            $this->notify(self::getByPk($id));
            if(IS_AJAX)
                exit;
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        throw new X3_404();        
    }
    
    public function actionFlats() {
        if(!IS_AJAX)
            throw new X3_404();
        $cid = (int)$_GET['cid'];
        $rid = (int)$_GET['rid'];
        $house = $_GET['house'];
        $uid = X3::user()->id;
        if(X3::user()->isKsk())
            $q = "SELECT flat FROM user_address WHERE user_id=$uid AND flat>0 AND city_id='$cid' AND region_id='$rid' AND house LIKE '$house' GROUP BY flat ORDER BY flat";
        else
            $q = "SELECT flat FROM user_address WHERE flat>0 AND city_id='$cid' AND region_id='$rid' AND house LIKE '$house' GROUP BY flat ORDER BY flat";
        $fq = X3::db()->query($q);
        $flats = array();
        while($f = mysql_fetch_assoc($fq)){
            $flats[] = $f['flat'];
        }
        echo json_encode($flats);
        exit;
    }
    
    public function notify($model) {
        if(X3::user()->isKsk()){
            $b = X3::db()->query("SELECT city_id, region_id, house FROM user_address WHERE user_id='$model->user_id' AND status=1");
            $o = array();
            while($bb = mysql_fetch_assoc($b)){
                $o['city_id'][] = $bb['city_id'];
                $o['region_id'][] = $bb['region_id'];
                $o['house'][] = $bb['house'];
            }
            $o['city_id'] = implode(', ', $o['city_id']);
            $o['region_id'] = implode(', ', $o['region_id']);
            $o['house'] = implode(', ', $o['house']);
            if($model->city_id > 0 && $model->region_id > 0 && $model->house != null && $model->flat != null){
                $c = "a1.city_id=$model->city_id AND a1.region_id=$model->region_id AND a1.house='$model->house' AND a1.flat='$model->flat'";
            }
            elseif($model->city_id > 0 && $model->region_id > 0 && $model->house != null && $model->flat == null){
                $c = "a1.city_id='$model->city_id' AND a1.region_id='$model->region_id' AND a1.house='$model->house'";
            }
            elseif($model->city_id > 0 && $model->region_id > 0 && $model->house == null && $model->flat == null){
                $c = "a1.house IN ({$o['house']}) AND a1.city_id='$model->city_id' AND a1.region_id='$model->region_id'";
            }
            elseif($model->city_id > 0 && $model->region_id == 0 && $model->house == null && $model->flat == null){
                $c = "a1.region_id IN ({$o['region_id']}) AND a1.house IN ({$o['house']}) AND a1.city_id='$model->city_id'";
            }else
                $c = "a1.city_id IN ({$o['city_id']}) AND a1.region_id IN ({$o['region_id']}) AND a1.house IN ({$o['house']})";
        }else{
            if($model->city_id > 0 && $model->region_id > 0 && $model->house != null && $model->flat != null){
                $c = "a1.city_id='$model->city_id' AND a1.region_id='$model->region_id' AND a1.house='$model->house' AND a1.flat='$model->flat'";
            }
            elseif($model->city_id > 0 && $model->region_id > 0 && $model->house != null && $model->flat == null){
                $c = "a1.city_id='$model->city_id' AND a1.region_id='$model->region_id' AND a1.house='$model->house'";
            }
            elseif($model->city_id > 0 && $model->region_id > 0 && $model->house == null && $model->flat == null){
                $c = "a1.city_id='$model->city_id' AND a1.region_id='$model->region_id'";
            }
            elseif($model->city_id > 0 && $model->region_id == 0 && $model->house == null && $model->flat == null){
                $c = "a1.city_id='$model->city_id'";
            }else
                $c = "1";
        }
        $users = X3::db()->query("SELECT u.id, CONCAT(name,' ',surname) username, u.email, u.phone FROM data_user u INNER JOIN user_address a1 ON a1.user_id=u.id WHERE 
            u.id<>$model->user_id AND $c
            GROUP BY u.id
            ");
        $uids = array();
        while($user = mysql_fetch_assoc($users)){
            $userset = X3::db()->fetch("SELECT * FROM user_settings us WHERE user_id='{$user['id']}'");
            if($userset['mailForum']){
                $uids[] = $user['id'];
                Notify::sendMail('newForum', array('text'=>$model->title,'name'=>$user['username'],'from'=>X3::user()->fullname,'link'=>X3::app()->baseUrl . '/forum/'.md5($model->title.$model->id).'.html'), $user['email']);
            }
            if($userset['smsForum'])
                Notify::sendSms('newForum', $user['phone'], array('forum'=>$model->title,'name'=>X3::user()->fullname));
        }
    }

    public function beforeValidate() {
        if($this->region_id == 0) $this->region_id = null;
        if($this->house == 0) $this->house = null;
        if($this->flat == 0) $this->flat = null;
        if(strpos($this->created_at,'.')!==false){
            $this->created_at = strtotime($this->created_at);
        }elseif($this->created_at == 0)
            $this->created_at = time();
    }
    
    public function beforeSave() {
        $this->updated_at = time();
        parent::beforeSave();
    }
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            Forum_Message::delete(array('forum_id'=>$model->id));
            Forum_Uploads::delete(array('forum_id'=>$model->id));
            Forum_Users::delete(array('forum_id'=>$model->id));
//            Forum_Notify::delete(array('forum_id'=>$model->id));
        }
        parent::onDelete($tables, $condition);
    }
    
    public static function search($word) {
        $id = X3::user()->id;
        $scope = array(
            '@select'=>'fm.content,f.title, f.id, fm.id message_id, um.id user_id, um.name, um.surname, um.kskname, um.ksksurname, um.image',
            '@from'=>array('forum_message'=>'fm'),
            '@group'=>'f.id'
        );
        if(X3::user()->isAdmin()){
            $scope['@join'] = "INNER JOIN data_forum f ON f.id=fm.forum_id INNER JOIN data_user u ON u.id=f.user_id INNER JOIN data_user um ON um.id=fm.user_id";
            $q = "fm.content LIKE '%$word%'";
        }else{
            $scope['@join'] = "INNER JOIN data_forum f ON f.id=fm.forum_id INNER JOIN data_user u ON u.id=f.user_id  INNER JOIN data_user um ON um.id=fm.user_id LEFT JOIN user_address a ON a.user_id=$id";
            $q = "(fm.content LIKE '%$word%') AND (
                    (f.user_id=$id)
                    OR
                    (
                        f.status AND 
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
                    (f.status AND u.role='ksk' AND
                     (
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                        (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                        (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                        (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                     )
                    )
                 )
                 ";
        }
        $scope['@condition'] = array('id'=>array('@@'=>$q));
        return $scope;
    }
}
?>
