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
        'content' => array('content', 'language'),
        'status' => array('boolean', 'default' => '0'),
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
                'user' => array('index', 'show', 'send', 'file','with','read','count'),
                'ksk' => array('index', 'show', 'send', 'file','with','read','count'),
                'admin' => array('index', 'show', 'send', 'file','with','read','count')
            ),
            'deny' => array(
                '*' => array('*'),
            ),
            'handle' => 'redirect:/user/login.html'
        );
    }

    public function err401() {
        header("HTTP/1.0 401 Authorization Required");
        echo '<h1>Доступ запрещен.</h1>';
        exit(0);
    }

    public function cache() {
        return array(
                //'cache'=>array('actions'=>'map','role'=>'*','expire'=>'+1 day','filename'=>'sitemap.xml','directory'=>X3::app()->basePath),
                //'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }

    public function route() { //Using X3_Request $url propery to parse
        return array(
            '/^sitemap\.xml$/' => 'actionMap',
            '/^download\/(.+?).html/' => array(
                'class' => 'Download',
                'argument' => '$1'
            )
        );
    }

    public function actionIndex() {
        $query = array();
        $query = array(array('m.user_to' => X3::user()->id),array('m2.user_from' => X3::user()->id));
        $query = array('@condition' => $query, 
            '@group' => 'data_user.id', 
            '@join'  => 'LEFT JOIN data_message m ON m.user_from=data_user.id LEFT JOIN data_message m2 ON m2.user_to=data_user.id',
            '@order' => 'm.status, m2.status, m.created_at DESC, m2.created_at DESC');
        $count = User::num_rows($query);
        $paginator = new Paginator(__CLASS__, $count);
        $query['@limit'] = $paginator->limit;
        $query['@offset'] = $paginator->offset;
        $models = User::get($query);

        $this->template->render('index', array('models' => $models, 'count' => $count, 'paginator' => $paginator));
    }
    
    public function actionWith() {
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
            $uq = X3::db()->query("SELECT id,CONCAT(name,' ',surname) name FROM data_user WHERE id=$id OR id=".X3::user()->id);
            while($u = mysql_fetch_assoc($uq)){
                $users[$u['id']] = $u['name'];
            }
            $this->template->render('show', array('models' => $models, 'count' => $count, 'paginator' => $paginator,'users'=>$users,'with'=>$id));
        }else
            throw new X3_404();
    }
    
    public function actionCount() {
        if(!IS_AJAX) throw new X3_404();
        echo Message::num_rows(array('status'=>'0','user_to'=>X3::user()->id));
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
                $mes->content = $message['content'];
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
                    echo json_encode (array('status'=>'ok','message'=>X3::translate('Сообщение успешно отправлено')));
                }else
                    echo json_encode (array('status'=>'error','message'=>X3::translate('Ошибка при заполнении формы')));
            }
        }else
            echo json_encode (array('status'=>'error','message'=>X3::translate('Ошибка при заполнении формы')));
        exit;
    }

}

?>
