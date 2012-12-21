<?php

class Subscribe extends X3_Module_Table {

    public $encoding = 'UTF-8';
    const KEY = 'X3_Subscribe';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_subscribe';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'email' => array('email', 'unique'),
        'key' => array('string[255]', 'default' => ''),
        'unkey' => array('string[255]', 'default' => ''),
        'status' => array('boolean', 'default' => '0'),
        //UNUSED
        'cookie'=>array('string','default'=>'NULL','unused'),        
    );
       
    public function _getCookie() {
        if(isset($_COOKIE[self::KEY]))
            return $_COOKIE[self::KEY];
        return null;
    }
    
    public function _setCookie($value) {
        if(isset($_COOKIE[self::KEY]))
            $cookie = $_COOKIE[self::KEY];
        else $cookie = array();
        if(!is_array($cookie)) $cookie = array();
        $cookie[] = $value;
        setcookie(self::KEY, json_encode($cookie), time()+86400, '/');//year
    }    

    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }

    public static function get($arr, $single = false, $class = __CLASS__) {
        return parent::get($arr, $single, $class);
    }

    public static function getByPk($pk, $class = __CLASS__) {
        return parent::getByPk($pk, $class);
    }

    public function moduleTitle() {
        return 'Рассылка';
    }
    
    public function fieldNames() {
        return array(
            'email' => 'Email',
            'status' => 'Отправлять письма',
        );
    }

    public function actionIndex() {
        if (isset($_POST) && $this->cookie==null) {
            if (!isset($_POST['email']))
                $_POST['email'] = '';
            $errors = array();
            $email = htmlspecialchars($_POST['email']);
            if(NULL == ($sub=Subscribe::get(array('email'=>$email)))){
                $sub = new Subscribe();
                $sub->email = $email;
            }
            $err = false;
            if($sub->getTable()->getIsNewRecord() && $sub->status){
                $this->cookie = $email;
                if(IS_SAME_DOMAIN)
                    $this->redirect($_SERVER['HTTP_REFERER']);
                else
                    $this->redirect('/');                
            }
            if (!$sub->getTable()->getIsNewRecord() || $sub->save()) {
                try {
                    $this->sendMail('', $email, $sub->key, $sub->unkey);
                    $this->cookie = $email;
                    if(IS_AJAX)
                        echo json_encode(array('status' => 'OK', 'message' => X3::translate('Вам на почту отправлено письмо с подтверждением на рассылку.')));
                    else {
                        if(IS_SAME_DOMAIN)
                            $this->redirect($_SERVER['HTTP_REFERER']);
                        else
                            $this->redirect('/');
                        X3_Session::writeOnce('subscribe', X3::translate('Вам на почту отправлено письмо с подтверждением на рассылку.'));
                    }
                } catch (Exception $e) {
                    $err = true;
                    $errors = array(array('Не удалось отправить письмо', $e->getMessage()));
                }
            } else {
                $err = true;
                $errors = $sub->table->getErrors();
            }
            if ($err) {
                $message = '';
                foreach ($errors as $err) {
                    $message .= implode("\n", $err) . "\n";
                }
                if(IS_AJAX)
                    echo json_encode(array('status' => 'ERROR', 'message' => $message));
                else {
                    if(IS_SAME_DOMAIN)
                        $this->redirect($_SERVER['HTTP_REFERER']);
                    else
                        $this->redirect('/');
                    X3_Session::writeOnce('subscribe', X3::translate('Вам на почту отправлено письмо с подтверждением на рассылку.'));
                }
            }
            exit;
        }
        throw new X3_404();
    }
    
    public static function isSent() {
        return(isset($_COOKIE[self::KEY]));
    }

    public function actionApprove() {
        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $l = X3::app()->db->fetch("SELECT * FROM `data_subscribe` WHERE `key`='" . mysql_real_escape_string($key) . "'");
            if ($l) {
                X3::app()->db->query("UPDATE `data_subscribe` SET `status`='1' WHERE `key`='" . mysql_real_escape_string($key) . "'");
                header('Location: /page/subscribed.html');
                die;
            } else {
                header('Location: /page/wrong_key.html');
                die;
            }
        }
        if (isset($_GET['unkey'])) {
            $key = $_GET['unkey'];
            $l = X3::app()->db->fetch("SELECT * FROM `data_subscribe` WHERE `unkey`='" . mysql_real_escape_string($key) . "'");
            if ($l) {
                unset($_COOKIE[self::KEY]);//giiiiii....
                setcookie(self::KEY, null, time()-86400, '/');
                X3::app()->db->query("UPDATE `data_subscribe` SET `status`='0' WHERE `unkey`='" . mysql_real_escape_string($key) . "'");
                header('Location: /page/unsubscribed.html');
                die;
            } else {
                throw new X3_404();
            }
        }
        exit;
    }

    public function actionCron() {
        //if ($_GET['key'] != 'qweqwe')
        //    exit;
        $model = SysSettings::get(array('name' => 'SubscribePeriod'), true);
        /*$last = SysSettings::get(array('name' => 'LastMailer'), true);
        if ($last == null) {
            SysSettings::getValue('LastMailer', 'integer', 'Последняя рассылка', '[INVISIBLE]', '0');
            $last = SysSettings::get(array('name' => 'LastMailer'), true);
        }*/
        //list($day, $week, $month) = explode(' ', $model->value);
        if($model->value == "0 0 0") exit;
        $period = "1 1 " . $model->value;

        $cron = new Cron();
        $cron->calcLastRan($period);
        $result=array('status'=>'OK');
        if (floor((time() - $cron->getLastRanUnix())/86400) == 0)
            $result = $this->fireSend();
        if ($result['status'] == 'ERROR')
            X3::log("ERROR in Subscribe/Cron:" . $result['message'],'apperr');
        exit;
    }

    public function beforeValidate() {
        if (empty($this->table['key']))
            $this->table['key'] = md5(time() . rand(50, 100));
        if (empty($this->table['unkey']))
            $this->table['unkey'] = md5(time() . rand(150, 200));
        return true;
    }

    protected function fireSend() {
        $models = Subscribe::get(array('status'));
        if ($models->count() == 0)
            $result = array('status' => 'ERROR', 'message' => 'Нет подписчиков');
        else {
            $items = array();
            $iis = Catalog::getInstance()->table->where("`issend`=0")->order('is_special DESC, created_at DESC')->asArray();
            if (empty($iis)) {
                $result = array('status' => 'ERROR', 'message' => 'Нет товаров для рассылки');
            }else
                foreach ($iis as $it) {
                    $items[$it['is_special']][] = $it;
                }
            unset($iis);
            $admin = SysSettings::getValue('AdminEmail');
            if (!empty($items))
                foreach ($models as $model) {
                    $from = 'Администрация Care.kz';
                    $reply = $admin;
                    $to = $model->email;
                    $subject = 'Рассылка с сайта Care.kz';
                    $body = $this->template->renderPartial('@views:admin:mail_template.php', array('items' => $items, 'unsubscribe' => $model->unkey));

                    $headers = "Content-type:text/html; charset=\"UTF-8\";\n";
                    $headers .= "From: $from<$reply>\n";
                    $headers .= "Sender: $from<$reply>\n";
                    $headers .= "Reply-To: $reply\n";
                    $headers .= "Content-type:text/html; charset=\"UTF-8\";";

                    try {
                        mail($to, $subject, $body, $headers);
                        if (!isset($result['status']))
                            $result['status'] = 'OK';
                    } catch (Exception $e) {
                        $result['status'] = 'ERROR';
                        $result['message'] .= $e->getMessage();
                    }
                }
            //if ($result['status'] == 'OK')                X3::app()->db->query("UPDATE `data_catalog` SET `issend`=1 WHERE `issend`=0");
        }
        return $result;
    }

    protected function sendMail($name, $to, $key, $ukey) {
        $from = SysSettings::getValue('AdminEmail');
        $reply = $from;
        $subject = "Подписка на рассылку с сайта maggroup.kz";
        $body = SysSettings::getValue('SubscribeMe', 'text', 'Текст письма подписки', 'Рассылка', 'Здравствуйте!<br/> 
                 Вы подписались на рассылку на сайте maggroup.kz.<br/>
                 Подтвердите свою подписку перейдя по ссылке [subscribe],<br/>
                 Или же если Вы этого не делали перейдите по ссылке [unsubscribe]<br/<hr/><br/>
                 С уважением, <br/>
                 Администрация.');
        $body = str_replace('[subscribe]', '<a href="'.X3::app()->baseUrl.'/subscribe/approve/key/' . $key . '.html">'.X3::translate('Подписаться').'</a>', $body);
        $body = str_replace('[unsubscribe]', '<a href="'.X3::app()->baseUrl.'/subscribe/approve/unkey/' . $ukey . '.html">'.X3::translate('Отписаться').'</a>', $body);
        $headers = "Content-type:text/html; charset=\"UTF-8\";\n";
        $headers .= "From: $from<$reply>\n";
        $headers .= "Sender: $from<$reply>\n";
        $headers .= "Reply-To: $reply\n";
        $headers .= "Content-type:text/html; charset=\"UTF-8\";";

        @mail($to, $subject, $body, $headers);
    }
    
    public static function prepareBody($body) {
        require_once X3::app()->basePath . '/application/helpers/SimpleDom.php';
        $html = str_get_html($body);
        foreach($html->find('img') as $img){
            if(substr($img->src, 0,1) == '/'){
                $img->src=X3::app()->baseUrl . $img->src;
            }
        }
        foreach($html->find('a') as $img){
            if(substr($img->href, 0,1) == '/'){
                $img->href=X3::app()->baseUrl . $img->href;
            }
        }
        return (string)$html;
    }
    
    public function execConf() {
        $model = SysSettings::get(array('name'=>'SubscribePeriod'),true);
        $templ = SysSettings::get(array('name'=>'SubscribeHandText'),true);
        if($model==null){
            $data = SysSettings::getValue('SubscribePeriod', 'string[255]', 'Период рассылки', 'Рассылка', '* * *');
            $model = SysSettings::get(array('name'=>'SubscribePeriod'),true);
        }
        if($templ==null){
            $data = SysSettings::getValue('SubscribeHandText', 'text', 'Шаблон ручной рассылки', 'Рассылка', '');
            $templ = SysSettings::get(array('name'=>'SubscribeHandText'),true);
        }
        if(isset($_POST['soso'])){
            $model->value = "{$_POST['value'][0]} {$_POST['value'][1]} {$_POST['value'][2]}";
            if($model->save()) $result = array('status'=>'OK');
            else {
                $err = $model->getErrors();
                $m = '';
                foreach($err as $e) $m.=implode('\n',$e).'\n';
                $result = array('status'=>'ERROR','message'=>$m);
            }
            $templ->value = $_POST['template'];
            if($templ->save()){
                X3_Session::writeOnce('success','Сохранено');
                X3::app()->module->redirect('/admin/subscribe');
            }
            
        }
        //$this->template->layout = null;
        X3::app()->module->template->addData(array('model'=>$model,'templ'=>$templ));
    }
    
    public function execManual() {
//        $this->template->layout = null;
//        $this->template->render('subscribe_manual');
    }
    
    public function execSendnow() {
        X3::app()->module->redirect('/subscribe/sendsub');
    }
    
    public function actionSendsub() {
        if(!X3::user()->isAdmin()) throw new X3_404();
        $result = array('message'=>'Разослано!');
        if($_GET['type']=='manual'){
            $send = $_POST['Send'];
            if(empty($send['users'])) {
                X3_Session::writeOnce('error','Не выбрано ни одного подписчика!');
                $this->redirect('/admin/subscribe/manual');
            }
            $models = Subscribe::get(array('id'=>array('IN'=>'('.implode(',',$send['users']).')')));
            $admin = SysSettings::getValue('AdminEmail');
            foreach($models as $model){
                $from = 'Администрация сайта maggroup.kz';
                $reply = $admin;
                $to = $model->email;
                $subject = !empty($send['title']) ? $send['title'] : 'Письмо с сайта maggroup.kz';
                $body = $send['body'];
                $body = self::prepareBody($body);
                $headers = "Content-type:text/html; charset=\"UTF-8\";\n";
                $headers .= "From: $from<$reply>\n";
                $headers .= "Sender: $from<$reply>\n";
                $headers .= "Reply-To: $reply\n";
                $headers .= "Content-type:text/html; charset=\"UTF-8\";";
                
                try{
                    mail($to,$subject,$body, $headers);
                    if(!isset($result['status'])) $result['status'] = 'success';
                    $result['message'] = 'Разослано!';
                }catch(Exception $e){
                    $result['status'] = 'error';
                    $result['message'] .= $e->getMessage();
                }
            }
        }else{
            $time = SysSettings::getValue('LastMailer', 'integer', 'Последняя рассылка', '[INVISIBLE]', '0');
            $news = News::get(array('@condition'=>array('status','created_at'=>array('>'=>$time)),'@order'=>'created_at DESC'));
            if(empty($news)) {
                X3_Session::writeOnce('error', 'Нет новых новостей для рассылки');
                $this->redirect('/admin/subscribe');
            }
            $models = Subscribe::get(array('status'));
            $admin = SysSettings::getValue('AdminEmail');
            foreach($models as $model){
                $from = 'Администрация maggroup.kz';
                $reply = $admin;
                $to = $model->email;
                $subject = 'Рассылка с сайта maggroup.kz';
                $body = $this->template->renderPartial('@app:modules:Admin:admin:templates:mail_template.php',array('models'=>$news,'unkey'=>$model->unkey));
                $body = self::prepareBody($body);
                $headers = "Content-type:text/html; charset=\"UTF-8\";\n";
                $headers .= "From: $from<$reply>\n";
                $headers .= "Sender: $from<$reply>\n";
                $headers .= "Reply-To: $reply\n";
                $headers .= "Content-type:text/html; charset=\"UTF-8\";";
                
                try{
                    mail($to,$subject,$body, $headers);
                    if(!isset($result['status'])) $result['status'] = 'success';
                }catch(Exception $e){
                    $result['status'] = 'error';
                    $result['message'] .= $e->getMessage();
                }            
            }
        }
        X3_Session::writeOnce($result['status'], $result['message']);
        if($result['status'] == 'success'){
            $this->redirect('/admin/subscribe');
        }else
            $this->redirect('/admin/subscribe/manual');
    }
    
    public function actionSavestack() {
        $ids = $_POST['stack'];
        $model = SysSettings::get(array('name'=>'SubscribeStack'),true);
        if($model==null){
            $data = SysSettings::getValue('SubscribeStack', 'string[255]', 'ID товаров на рассылку', 'Рассылка', '');
            $model = SysSettings::get(array('name'=>'SubscribeStack'),true);
        }
        $model->value = $ids;
        $model->save();
        exit;
    }    

}