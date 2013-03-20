<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class User_Report extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'user_report';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'file_id'=>array('string[128]','index','ref'=>array('Uploads','id','default'=>"name")),
        'user_id'=>array('integer[10]','unsigned','index','ref'=>array('User','id','default'=>"name")),
        'city_id' => array('integer[10]', 'unsigned', 'default'=>'NULL', 'index', 'ref'=>array('City','id','default'=>'title','null'=>'Все','query'=>array())),
        'region_id' => array('integer[10]', 'unsigned','default'=>'NULL', 'index', 'ref'=>array('City_Region','id','default'=>'title')),
        'house'=>array('string[10]','default'=>"NULL"),
        'flat'=>array('string[10]','default'=>"NULL"),
        'title'=>array('content[255]'),
        'status'=>array('boolean','default'=>'0'),
        'created_at'=>array('datetime'),
    );
    
    public function __construct($action = null) {
        if(X3::user()->isKsk())
            $this->_fields['city_id']['ref']['query'] = array(
                '@join'=>"INNER JOIN user_address a ON a.city_id=data_city.id",
                '@condition'=>array('a.user_id'=>X3::user()->id)
                );
        parent::__construct($action);
    }

    public function fieldNames() {
        return array(
            'user_id'=>'Автор',
            'file_id'=>X3::translate('Отчет'),
            'city_id'=>X3::translate('Регион'),
            'region_id'=>X3::translate('Улица'),
            'house' => X3::translate('№ дома'),
            'flat' => X3::translate('№ квартиры'),
            'title'=>X3::translate('Заголовок'),
            'created_at'=>X3::translate('Дата отчета'),
            'status'=>'Пуликован',
        );
    }
    
    public function filter() {
        return array(
            'allow' => array(
                'user' => array('index', 'get','send','with','read','count'),
                'ksk' => array('index', 'get','send','with','read','count','create','flats','delete'),
                'admin' => array('index', 'get','send', 'file','with','read','count','create','flats','delete')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }
    
    
    public static function isMyReport($id) {
        if(X3::user()->isAdmin())
            return true;
        $uid = X3::user()->id;
        $q = "SELECT f.id FROM user_report f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$uid WHERE 
            f.id='$id' AND ((
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
        return X3::db()->call("isMyReport",$id,$uid) > 0;
    }


    public function actionIndex() {
        $id = X3::user()->id;
        $filter = "";
        $from = "";
        if(isset($_GET['by'])){
            $filter = " AND f.user_id='".((int)$_GET['by'])."'";
            $tmp = User::getByPk((int)$_GET['by']);
            if($tmp){
                $from = $tmp->getFullname();
                unset($tmp);
            }
        }
        if(X3::user()->isAdmin()){
            $q = "FROM user_report f WHERE 1 $filter";
        }else if(X3::user()->isKsk()){
            $q = "FROM user_report f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE
                (f.user_id=$id)
                 ";
        }else
            $q = "FROM user_report f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE (
                (
                    f.status AND u.role='admin' AND 
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
                    f.status AND u.role='ksk' AND
                  (
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                    (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                    (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                    (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address WHERE user_id=u.id AND status=1) AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1))
                  )
                )
                )$filter
                 ";
        $count = X3::db()->count("SELECT f.id, MAX(f.created_at) latest ".$q." GROUP BY f.id");
        $paginator = new Paginator(__CLASS__, $count);
        $q = "SELECT f.id, f.title, f.user_id, MAX(f.created_at) latest, f.status, f.file_id, f.city_id, f.region_id, f.house, f.flat " . $q . " GROUP BY f.id ORDER BY latest DESC LIMIT $paginator->offset,$paginator->limit";
        $models = X3::db()->query($q);
        $this->template->render('@views:user:report.php', array('models' => $models, 'count' => $count, 'paginator' => $paginator,'from'=>$from));
    }
    
    public function actionCreate() {
        $id = X3::user()->id;
        if(isset($_GET['id']) && (int)$_GET['id']>0){
            $model = self::getByPk((int)$_GET['id']);
        }else{
            $model = new self();
        }
        if(isset($_POST[__CLASS__])){
            $data = $_POST[__CLASS__];
            $model->getTable()->acquire($data);
            if($model->getTable()->getIsNewRecord())
                $model->user_id = $id;
            if(isset($_POST['public']))
                $model->status = '1';
            if (isset($_FILES['file']) && $_FILES['file']['error']==0) {
                $h = new Upload('file');
                if($h->message!='')
                    $model->addError('file_id',$h->message);
                $orig = $h->filename;
                $ext = strtolower(pathinfo($orig,PATHINFO_EXTENSION));
                $allowed = SysSettings::getValue('Message_Uploads.Extensions', 'string', 'Разрешенные к загрузке расширения файлов', '[INVISIBLE]', 'jpg,png,gif,tif,rar,zip,doc,docx,xls,xlsx,txt,ppt,pptx');
                $allowed = array_map(function($item){return trim($item);},explode(',',$allowed));
                if(!in_array($ext,$allowed))
                    $model->addError('file_id',$ext.". ".strtr(X3::translate('Возможно загрузить только файлы с расширениями: {files}'),array('{files}'=>implode(', ',$allowed)))); 
                $filename = md5_file($h->tmp_name);
                if ($h->saveAs($filename)) {
                    if (NULL === Uploads::getByPk($filename)) {
                        $mf = new Uploads();
                        $mf->id = $filename;
                        $mf->name = $orig;
                        $mf->created_at = time();
                        $mf->save();
                    }
                    $model->file_id = $filename;
                } else {
                    $model->addError('file_id',$h->message);
                }
            }elseif($model->getTable()->getIsNewRecord())
                $model->file_id = NULL;
            if(!$model->hasErrors() && $model->save()){
                if(isset($_POST['public'])){
                    $this->notify($model);
                }
                $this->redirect('/reports/');
            }
        }
        X3::app()->datapicker = true;
        $this->template->render('@views:user:report_form.php', array('model' => $model));
    }

    public function actionCount() {
        if(!IS_AJAX) throw new X3_404();
        echo Message::num_rows(array('status'=>'0','user_to'=>X3::user()->id));
        exit;
    }
    
    public function actionDelete(){
        if(!X3::user()->isAdmin()){
            $id = X3::user()->id;
            if(isset($_GET['id']) && (int)$_GET['id']>0){
                self::delete(array('user_id'=>$id,'id'=>(int)$_GET['id']));
            }
        }else {
            if(isset($_GET['id']) && (int)$_GET['id']>0)
                self::deleteByPk((int)$_GET['id']);
        }
        $this->redirect('/reports/');
    }


    public function actionRead() {
        $id = (int)$_GET['id'];
        $uid = X3::user()->id;
        $answer = (int)$_GET['val'];
        $vote = self::getByPk($id);
        if($vote == NULL){
            if(IS_AJAX)
                exit;
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $author = User::getByPk($vote->user_id);
        $c = "1";
        $ax = "a1.city_id IN (SELECT city_id FROM user_address WHERE user_id=$vote->user_id AND status=1)";
        if($author->role == 'ksk'){
            $b = X3::db()->query("SELECT city_id, region_id, house FROM user_address WHERE user_id='$vote->user_id' AND status=1");
            $o = array();
            while($bb = mysql_fetch_assoc($b)){
                $o['city_id'][] = $bb['city_id'];
                $o['region_id'][] = $bb['region_id'];
                $o['house'][] = $bb['house'];
            }
            $o['city_id'] = implode(', ', $o['city_id']);
            $o['region_id'] = implode(', ', $o['region_id']);
            $o['house'] = implode(', ', $o['house']);
            if($vote->city_id > 0 && $vote->region_id > 0 && $vote->house != null && $vote->flat != null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id' AND a2.house='$vote->house' AND a2.flat='$vote->flat'";
            }
            elseif($vote->city_id > 0 && $vote->region_id > 0 && $vote->house != null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id' AND a2.house='$vote->house'";
            }
            elseif($vote->city_id > 0 && $vote->region_id > 0 && $vote->house == null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.house IN ({$o['house']}) AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id'";
            }
            elseif($vote->city_id > 0 && $vote->region_id == 0 && $vote->house == null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.region_id IN ({$o['region_id']}) AND a1.house IN ({$o['house']}) AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id'";
            }else
                $c = "a1.user_id='$uid' AND a1.city_id IN ({$o['city_id']}) AND a1.region_id IN ({$o['region_id']}) AND a1.house IN ({$o['house']}) AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat";
        }else{
            if($vote->city_id > 0 && $vote->region_id > 0 && $vote->house != null && $vote->flat != null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id' AND a2.house='$vote->house' AND a2.flat='$vote->flat'";
            }
            elseif($vote->city_id > 0 && $vote->region_id > 0 && $vote->house != null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id' AND a2.house='$vote->house'";
            }
            elseif($vote->city_id > 0 && $vote->region_id > 0 && $vote->house == null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id' AND a2.region_id='$vote->region_id'";
            }
            elseif($vote->city_id > 0 && $vote->region_id == 0 && $vote->house == null && $vote->flat == null){
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat AND a2.city_id='$vote->city_id'";
            }else
                $c = "a1.user_id='$uid' AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house AND a1.flat=a2.flat";
        }
        $qa = X3::db()->query("SELECT a2.id FROM user_address a1, user_address a2 WHERE $c GROUP BY a2.id");
        while($address = mysql_fetch_assoc($qa)){
            $ws = new Vote_Stat;
            $ws->address_id = $address['id'];
            $ws->vote_id = $id;
            $ws->answer = $answer;
            $ws->save();
        }
        if(IS_AJAX)
            exit;
        $this->redirect($_SERVER['HTTP_REFERER']);
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
        if(!X3::user()->isAdmin()){
            if (isset($_GET['id']) && ($id = (int)$_GET['id'])>0) {
                $a = self::update(array('status'=>'1'),array('user_id'=>X3::user()->id,'id'=>$id));
                $this->notify(self::getByPk($id));
                if(IS_AJAX)
                    exit;
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
        }else
            if (isset($_GET['id']) && ($id = (int)$_GET['id'])>0) {
                $a = self::update(array('status'=>'1'),array('id'=>$id));
                $this->notify(self::getByPk($id));
                if(IS_AJAX)
                    exit;
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
        throw new X3_404();
    }
    
    public function actionGet() {
        $id = $_GET['id'];
        $rep = self::getByPk($id);
        if($rep == null || !self::isMyReport($id))
            throw new X3_404();
        $model = Uploads::getByPk($rep->file_id);
        if($model == null) 
            throw new X3_404();
        $file = X3::app()->basePath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $model->id;
        if (file_exists($file)) {
            if(NULL == Report_Stat::get(array('report_id'=>$rep->id,'user_id'=>X3::user()->id),1)){
                $n = new Report_Stat();
                $n->report_id = $rep->id;
                $n->user_id = X3::user()->id;
                $n->save();
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$model->name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
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
            $q = "SELECT flat FROM user_address INNER JOIN data_user u ON u.id=user_id WHERE u.role='user' AND user_id<>$uid AND flat>0 AND city_id='$cid' AND region_id='$rid' AND house='$house' GROUP BY flat ORDER BY flat";
        else
            $q = "SELECT flat FROM user_address WHERE flat>0 AND city_id='$cid' AND region_id='$rid' AND house='$house' GROUP BY flat ORDER BY flat";
        $fq = X3::db()->query($q);
        echo X3::db()->getErrors();
        $flats = array();
        while($f = mysql_fetch_assoc($fq)){
            $flats[] = $f['flat'];
        }
        echo json_encode($flats);
        exit;
    }
    
    public function notify($model) {
        $id = X3::user()->id;
        if(X3::user()->isKsk()){
            $role = "role='user' AND ";
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
            $role = "";
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
        $users = X3::db()->query("SELECT CONCAT(name,' ',surname) username, email FROM data_user u INNER JOIN user_address a1 ON a1.user_id=u.id WHERE 
            u.id<>$id AND ($role $c)
            GROUP BY u.id
            ");
        while($user = mysql_fetch_assoc($users)){
        //                        var_dump($user);
            Notify::sendMail('newReport', array('text'=>$model->title,'name'=>$user['username'],'from'=>X3::user()->fullname,'link'=>X3::app()->baseUrl.'/reports/'), $user['email']);
        }
    }

    public function beforeValidate() {
        if($this->city_id == 0) $this->city_id = null;
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
            X3::db()->query("DELETE FROM user_report_stat WHERE report_id='$model->id'");
        }
        parent::onDelete($tables, $condition);
    }
    
    public static function search($word) {
        $id = X3::user()->id;
        $scope = array(
            '@select'=>'f.title, f.id,u.id as user_id, u.name, u.surname, u.kskname, u.ksksurname, u.image',
            '@from'=>array('user_report'=>'f'),
            '@group'=>'f.id',
        );
        if(X3::user()->isAdmin()){
            $scope['@join'] = "INNER JOIN data_user u ON u.id=f.user_id";
            $q = "f.title LIKE '%$word%'";
        }else{
            $scope['@join'] = "INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id";
            $q = "(f.title LIKE '%$word%') AND (
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
