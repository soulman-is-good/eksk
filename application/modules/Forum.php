<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class Forum extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
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
        'created_at'=>array('datetime')
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
    
    public function filter() {
        return array(
            'allow' => array(
                'user' => array('index', 'show', 'send', 'file','with','read','count'),
                'ksk' => array('index', 'show', 'send', 'file','with','read','count','create','flats'),
                'admin' => array('index', 'show', 'send', 'file','with','read','count','create','flats')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }
    
    public function actionIndex() {
        $id = X3::user()->id;
        $q = "FROM data_forum f, user_address a WHERE 
            a.user_id=$id AND (
            (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
            (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
            (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL) OR 
            (f.city_id=a.city_id AND f.region_id IS NULL))";
        $count = X3::db()->count("SELECT f.id ".$q);
        $paginator = new Paginator(__CLASS__, $count);
        $q = "SELECT f.id, f.title, f.user_id, MAX(f.created_at) latest " . $q . " GROUP BY f.id ORDER BY latest DESC LIMIT $paginator->offset,$paginator->limit";
        $models = X3::db()->query($q);
        $this->template->render('index', array('models' => $models, 'count' => $count, 'paginator' => $paginator));
    }
    
    public function actionShow() {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $id = (int) $_GET['id'];
            $query = array(array('user_from' => $id,'user_to'=>X3::user()->id),array('user_to' => $id,'user_from'=>X3::user()->id));
            $query = array('@condition' => $query, '@order' => 'created_at DESC');
            $count = self::num_rows($query);
            $paginator = new Paginator(__CLASS__, $count);
            $query['@limit'] = $paginator->limit;
            $query['@offset'] = $paginator->offset;
            $models = self::get($query);
            $users = array();
            $uq = X3::db()->query("SELECT id,CONCAT(name,' ',surname) name, image, role FROM data_user WHERE id=$id OR id=".X3::user()->id);
            while($u = mysql_fetch_assoc($uq)){
                if($u['image']=='' || $u['image']==null || !is_file('uploads/User/'.$u['image']))
                    $image = '/images/default.png';
                else
                    $image = '/uploads/User/100x100/'.$u['image'];
                $users[$u['id']] = array('title'=>$u['role']=='admin'?X3::translate('Администратор').'#'.$u['id']:$u['name'],'avatar'=>$image);
            }
            $this->template->render('show', array('models' => $models, 'count' => $count, 'paginator' => $paginator,'users'=>$users,'with'=>$id));
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
                        '@order'=>'created_at DESC'
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
            $model->user_id = $id;
            if($model->save()){
                if(trim($msg['content'])!='' && trim($msg['files'],', ')!=''){
                    if($message->getTable()->getIsNewRecord())
                        $message->id = $msg['id'];
                    $message->forum_id = $model->id;
                    $message->user_to = NULL;
                    $message->content = $msg['content'];
                    if($message->save()){
                        $files = explode(',',$msg['files']);
                        foreach($files as $file){
                            $file = trim($file);
                            if($file == '') continue;
                            $F = new Forum_Uploads();
                            $F->file_id = $file;
                            $F->message_id = $message->id;
                            $F->created_at = time();
                            $F->save();
                        }
                    }
                }
                $this->redirect('/forum/');
            }
        }
        $this->template->render('form', array('model' => $model,'message'=>$message));
    }

    public function actionCount() {
        if(!IS_AJAX) throw new X3_404();
        echo Message::num_rows(array('status'=>'0','user_to'=>X3::user()->id));
        exit;
    }
    
    public function actionRead() {
        if(!IS_AJAX) throw new X3_404();
        $id = (int)$_GET['id'];
        Forum_Message::update(array('status'=>'1'),array('user_to'=>X3::user()->id,'id'=>$id));
        exit;
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
                        $F = new Forum_Uploads();
                        $F->file_id = $file;
                        $F->message_id = $mes->id;
                        $F->created_at = time();
                        $F->save();
                    }
                    $userto = X3::db()->fetch("SELECT email FROM data_user WHERE id='$user_to'");
                    Notify::sendMail('NewMessage',array('name'=>X3::user()->fullname,'message'=>nl2br($mes->content)),$userto['email']);
                    echo json_encode (array('status'=>'ok','message'=>X3::translate('Сообщение успешно отправлено')));
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
    
    public function actionFlats() {
        if(!IS_AJAX)
            throw new X3_404();
        $cid = (int)$_GET['cid'];
        $rid = (int)$_GET['rid'];
        $house = $_GET['house'];
        $fq = X3::db()->query("SELECT flat FROM user_address WHERE city_id='$cid' AND region_id='$rid' AND house LIKE '$house' GROUP BY flat ORDER BY flat");
        $flats = array();
        while($f = mysql_fetch_assoc($fq)){
            $flats[] = $f['flat'];
        }
        echo json_encode($flats);
        exit;
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
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            Forum_Message::delete(array('forum_id'=>$model->id));
        }
        parent::onDelete($tables, $condition);
    }
}
?>
