<?php
/**
 * Forum
 *
 * @author Soul_man
 */
class Warning extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_warning';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'user_id'=>array('integer[10]','unsigned','index','ref'=>array('User','id','default'=>"name")),
        'city_id' => array('integer[10]', 'unsigned', 'default'=>'NULL', 'index', 'ref'=>array('City','id','default'=>'title','null'=>'Все',
                'query'=>array()
            )),
        'region_id' => array('integer[10]', 'unsigned','default'=>'NULL', 'index', 'ref'=>array('City_Region','id','default'=>'title')),
        'house'=>array('string[10]','default'=>"NULL"),
        'flat'=>array('string[10]','default'=>"NULL"),
        'title'=>array('content[1024]'),
        'status'=>array('boolean','default'=>'0'),
        'type'=>array('enum["*","admin","ksk","user"]','default'=>"*"),
        'created_at'=>array('datetime'),
        'end_at'=>array('datetime')
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
            'city_id'=>X3::translate('Регион'),
            'region_id'=>X3::translate('Улица'),
            'house' => X3::translate('№ дома'),
            'flat' => X3::translate('№ квартиры'),
            'title'=>X3::translate('Текст оповещения'),
            'type'=>X3::translate('Кому'),
            'end_at'=>X3::translate('Дата окончания'),
            'status'=>'Пуликован',
        );
    }
    
    public function filter() {
        return array(
            'allow' => array(
                'user' => array('index', 'show', 'send', 'file','with','read','count'),
                'ksk' => array('index', 'show', 'send', 'file','with','read','count','create','flats','delete'),
                'admin' => array('index', 'show', 'send', 'file','with','read','count','create','flats','delete')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }
    
    public static function isKnown($id){
        $uid = X3::user()->id;
        return X3::db()->count("SELECT id FROM warning_stat WHERE warning_id=$id AND user_id=$uid")>0;
    }


    public function actionIndex() {
        $id = X3::user()->id;
        $date = X3::db()->fetch("SELECT created_at FROM data_user WHERE id=$id");
        $type = X3::user()->isUser()?"(f.type='user' OR f.type='*')":(X3::user()->isKsk()?"(f.type='ksk' OR f.type='*')":"(f.type='admin' OR f.type='*')");
        $q = "FROM data_warning f INNER JOIN data_user u ON u.id=f.user_id LEFT JOIN user_address a ON a.user_id=$id WHERE
            f.end_at>={$date['created_at']} AND (
            (f.user_id=$id)
                OR
            (f.city_id IS NULL AND $type AND u.role='admin')
                OR
            ($type AND u.role='ksk' AND
             (
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat=a.flat) OR 
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house=a.house AND f.flat IS NULL) OR 
                (f.city_id=a.city_id AND f.region_id=a.region_id AND f.house IS NULL AND f.flat IS NULL AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR 
                (f.city_id=a.city_id AND f.region_id IS NULL AND a.region_id IN (SELECT region_id FROM user_address WHERE user_id=u.id AND status=1) AND a.house IN (SELECT house FROM user_address WHERE user_id=u.id AND status=1)) OR
                (f.city_id IS NULL AND a.city_id IN (SELECT city_id FROM user_address aa WHERE aa.user_id=u.id AND aa.status=1) AND a.region_id IN (SELECT region_id FROM user_address aa WHERE aa.user_id=u.id AND aa.status=1) AND a.house IN (SELECT house FROM user_address aa WHERE aa.user_id=u.id AND aa.status=1))
             )
             )
            )
             ";
        $count = X3::db()->count("SELECT f.id, MAX(f.created_at) latest ".$q);
        $paginator = new Paginator(__CLASS__, $count);
        $q = "SELECT f.id, f.title, f.user_id, MAX(f.created_at) latest, f.status " . $q . " GROUP BY f.id ORDER BY latest DESC LIMIT $paginator->offset,$paginator->limit";
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
        if(isset($_GET['id']) && (int)$_GET['id']>0){
            $model = Warning::getByPk((int)$_GET['id']);
        }else{
            $model = new Warning();
        }
        if(isset($_POST['Warning'])){
            $data = $_POST['Warning'];
            $model->getTable()->acquire($data);
            $model->user_id = $id;
            if(X3::user()->isKsk())
                $model->type = 'user';
            if(isset($_POST['public']))
                $model->status = '1';
            if($model->save()){
                $this->redirect('/warning/');
            }
        }
        X3::app()->datapicker = true;
        $this->template->render('form', array('model' => $model));
    }

    public function actionCount() {
        if(!IS_AJAX) throw new X3_404();
        echo Message::num_rows(array('status'=>'0','user_to'=>X3::user()->id));
        exit;
    }
    
    public function actionDelete(){
        $id = X3::user()->id;
        if(isset($_GET['id']) && (int)$_GET['id']>0){
            Warning::delete(array('user_id'=>$id,'id'=>$_GET['id']));
        }
        $this->redirect('/warning/');
    }


    public function actionRead() {
        $id = (int)$_GET['id'];
        if(Warning_Stat::get(array('user_id'=>X3::user()->id,'warning_id'=>$id),1)!=null){
            if(IS_AJAX)
                exit;
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $ws = new Warning_Stat;
        $ws->user_id = X3::user()->id;
        $ws->warning_id = $id;
        $ws->save();
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
        if (isset($_GET['id']) && ($id = (int)$_GET['id'])>0) {
            $a = Warning::update(array('status'=>'1'),array('user_id'=>X3::user()->id,'id'=>$id));
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

    public function beforeValidate() {
        if($this->city_id == 0) $this->city_id = null;
        if($this->region_id == 0) $this->region_id = null;
        if($this->house == 0) $this->house = null;
        if($this->flat == 0) $this->flat = null;
        if(strpos($this->created_at,'.')!==false){
            $this->created_at = strtotime($this->created_at);
        }elseif($this->created_at == 0)
            $this->created_at = time();
        if(strpos($this->end_at,'.')!==false){
            $time = strtotime($this->end_at);
            $this->end_at = mktime(23,59,59,date('n',$time), date('j',$time), date('Y',$time));
        }elseif($this->end_at == 0)
            $this->end_at = time() + 84600;
    }
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
            Warning_Stat::delete(array('warning_id'=>$model->id));
        }
        parent::onDelete($tables, $condition);
    }
}
?>
