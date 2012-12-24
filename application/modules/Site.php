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
class Site extends X3_Module {

    public static function newInstance($class=null) {
        return parent::newInstance(__CLASS__);
    }
    public static function getInstance($class=null) {
        return parent::getInstance(__CLASS__);
    }
    
    public function filter() {
        return array(
        /*    'allow'=>array(
                '*'=>array('*'),
                'user'=>array('map','limit','feedback','error'),
                'sale'=>array('map','limit','feedback','error'),
                'admin'=>array('map','limit','feedback','error')
            ),
            'deny'=>array(
                '*'=>array('*'),
                'admin'=>array('index'),
                'user'=>array('index'),
                'sale'=>array('index')
            ),
            'handle'=>'redirect:/user/settings.html'*/
        );
    }
    
    public function err401(){
            header("HTTP/1.0 401 Authorization Required");
            echo '<h1>Доступ запрещен.</h1>';
            exit(0);
    }
    public function cache(){
        return array(
            //'cache'=>array('actions'=>'map','role'=>'*','expire'=>'+1 day','filename'=>'sitemap.xml','directory'=>X3::app()->basePath),
            //'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }

    public function route() { //Using X3_Request $url propery to parse
        return array(
            '/^sitemap\.xml$/'=>'actionMap',
            '/^download\/(.+?).html/'=>array(
                'class'=>'Download',
                'argument'=>'$1'
            )
        );
    }
    public function actionIndex() {
        $user = User::getByPk(X3::user()->id);
        if($user == null)
            throw new X3_404();
        
        $this->template->render('index',array('user'=>$user));
    }

    public function actionMap() {
        if (!isset($_GET['type']) || $_GET['type'] != 'xml') {
            $this->template->render('sitemap');
        }else{
            //throw new X3_404();
            $this->template->layout = null;
            $links = array();
            $i=0;
            $file = 0;
            $files = array();
            //NEWS
            $models = X3::db()->query("SELECT id FROM data_news WHERE status");
            while($m = mysql_fetch_assoc($models)){
                $links[] = str_replace(array('&',"'",'"','>','<'),array('&amp;','&apos;','&quot;','&gt;','&lt;'),X3::app()->baseUrl . "/news/" . $m['id'] . ".html");
                $i++;
                if($i%50000==0){
                    $fname=$file==0?"sitemap.xml":"sitemap".($file-1).".xml";file_put_contents(X3::app()->basePath."/$fname",  X3_Widget::run('@views:site:map.php',array('links'=>$links)));
                    $links = array();
                    $files[] = $fname;
                    $file++;
                }
                
            }
            if($i%50000!=0){
                $fname=$file==0?"sitemap.xml":"sitemap".($file-1).".xml";file_put_contents(X3::app()->basePath."/$fname",  X3_Widget::run('@views:site:map.php',array('links'=>$links)));
                $links = array();
                $files[] = $fname;
            }
            $robot  = "User-agent: *\r\n";
            $robot .= "Disallow: /admin\r\n";
            $robot .= "Disallow: /login\r\n";
            $robot .= "Host: www.kansha.kz\r\n";
            $robot .= "Sitemap: http://www.kansha.kz/sitemap.xml\r\n";
            for($j=0;$j<$file;$j++){
                $robot .= "Sitemap: http://www.kansha.kz/sitemap$j.xml\r\n";
            }
            file_put_contents(X3::app()->basePath.'/robots.txt', $robot);
            if($file>0){
                echo $i." links generated.";
                $index = X3_Widget::run('@views:site:map_index.php',array('files'=>$files));
                file_put_contents(X3::app()->basePath.'/sitemap_index.xml',$index);
                //header ("content-type: text/xml");
                //echo $index;
            }else{
                echo $i." links generated.";
                //header ("content-type: text/xml");
                //echo file_get_contents(X3::app()->basePath.'/sitemap.xml');
            }
            exit;
            //$this->template->render('map',array('links'=>$links));
        }
    }

    public function actionFeedback() {
        $fos = array('subject'=>'','text'=>'','name'=>'','email'=>'','company'=>'','city'=>'','phone'=>'');
        $success=false;
        $errors = array();
        if(isset($_POST['Fos'])){
            $fos = array_extend($fos,$_POST['Fos']);
            X3::import('application:extensions:swift:swift_required.php',true);
            $transport = Swift_MailTransport::newInstance();
            $user = User::getByPk(X3::app()->user->id);
            $subject = strip_tags($fos['subject']);
            $subject = trim($subject);
            $name = strip_tags($fos['name']);
            $name = trim($name);
            $city = strip_tags($fos['city']);
            $city = trim($city);
            $company = strip_tags($fos['company']);
            $phone = strip_tags($fos['phone']);
            $text = strip_tags($fos['text']);
            $email = strip_tags($fos['email']);
            $text = trim($text);
            $text = nl2br($text);
            if(empty($name))
                $errors['name'] = 'Поле Имя не заполнено.';
            if(empty($city))
                $errors['city'] = 'Поле Город не заполнено.';
            if(empty($text))
                $errors['text'] = 'Поле Сообщение не заполнено.';
            if(empty($email))
                $errors['email'] = 'Поле E-mail не заполнено.';
            elseif(preg_match("/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD", $email)==0 && $email!=''){
                $errors['email'] = 'Поле Email заполнено не корректно.';
            }
            if(empty($errors)){
                $Aemail = Syssettings::getValue('AdminEmail','string[255]','Email администратора','Настройки','soulman.is.good@gmail.com');
                $message = Swift_Message::newInstance()->setSubject('Письмо от пользователя '.$name)->setTo($Aemail);
                if($fos['email']!='')
                    $message->setFrom($fos['email']);
                $date = date("d.m.Y H:i");
                $message->setBody("Пользователь с именем <strong>$name</strong>({$_SERVER['REMOTE_ADDR']}) $date написал сообщение с темой '{$subject}'<br/>$text<hr/>
                <b>Прочие данные:</b><br/>Имя: $name<br />Компания: $company<br/>Город: $city<br/>Телефон: $phone<br/>Email: $email<br />", 'text/html');
                $mailer = Swift_Mailer::newInstance($transport);
                try {
                    $mailer->send($message);
                    $success='Сообщение успешно отправлено!';
                    X3_Session::getInstance()->writeOnce('fos',$success);
                    $this->controller->refresh();
                    //$fos = array('subject'=>'','text'=>'','name'=>'','email'=>'','company'=>'','city'=>'','phone'=>'');
                } catch (Exception $e) {
                    $errors['common']=$e->getMessage();
                    exit;
                }
            }
        }
        $this->template->render('feedback',array('fos'=>$fos,'errors'=>$errors,'fsuccess'=>$success));
    }

    public function actionContacts() {        
        $this->template->render('contacts',array());
    }

    public function actionLimit() {
        $limit = (int)$_POST['val'];
        if($limit<=0 || !IS_AJAX) exit;
        $model = ucfirst($_POST['module']);
        $path = X3::app()->getPathFromAlias("@app:modules:$model.php");
        if(!is_file($path)) exit;
        $model = $model.'Limit';
        X3::app()->user->$model = $limit;
        echo 'OK';
        exit;
    }
    
    public function actionWeights() {
        echo '1111';
        $model = ucfirst($_POST['module']);
        $ids = explode(',',$_POST['ids']);
        if(empty($ids)) exit;
        $tablename = X3_Module_Table::getInstance($model)->tableName;
        X3::db()->startTransaction();
        foreach ($ids as $i=>$id){
            if($id>0)
                X3::db()->addTransaction("UPDATE `$tablename` SET `weight`='$i' WHERE id='$id'");
        }
        X3::db()->commit();
        exit;
    }
    

    public function actionError() {
        $page = Page::get(array('name'=>'error404'),1);
        if($page == null){
            $page = new Page;
            $page->name = 'error404';
            $page->title = 'Страница не найдена';
            $page->text = 'Страница не найдена';
            $title = "title";
            $text = "text";
            foreach(X3::app()->languages as $lang){
                $page->{"{$title}_{$lang}"} = 'Страница не найдена';
                $page->{"{$text}_{$lang}"} = 'Страница не найдена';
            }
            $page->save();
        }
        $this->template->render('error',array('model'=>$page));
    }
}
?>
