<?php
/**
 * Description of Uploads
 *
 * @author Soul_man
 */

/**
 * @property string $id primary key
 * @property integer $created_at unix timestamp
 */
class Uploads extends X3_Module_Table {

    public $encoding = 'UTF-8';

    public $tableName = 'data_uploads';

    public $_fields = array(
      'id'=>array('string[128]','primary'),
      'name'=>array('string[256]','default'=>'NULL'),
      'created_at'=>array('integer[10]','unsigned','default'=>'0')
    );
    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }
    
    public function beforeAction(&$action) {
        if ($this->controller->action == 'captcha' || $this->controller->action == 'get')
            return true;
        if(!X3_DEBUG && !X3::user()->isAdmin())
            throw new X3_404();
        $act = $action;
        //$act = str_replace('action', '', $act);
        $resize = new Resize($act);
        exit;
        return false;
    }

    public function actionCaptcha() {
        header('Content-type: image/gif');
        if (is_array(X3::app()->user->captcha) && X3::app()->user->captcha['times'] > 0 && is_file('uploads/' . X3::app()->user->captcha['file']) && !isset($_GET['f5'])) {
            $arr = X3::app()->user->captcha;
            if (time() - $arr['lasttime'] > 3) {
                $arr['times'] = $arr['times'] - 1;
                $arr['lasttime'] = time();
            }
            X3::app()->user->captcha = $arr;
            echo file_get_contents('uploads/' . X3::app()->user->captcha['file']);
            exit;
        } elseif (is_array(X3::app()->user->captcha) && is_file('uploads/' . X3::app()->user->captcha['file'])) {
            @unlink('uploads/' . X3::app()->user->captcha['file']);
        }
        if(!$this->cleanCaptcha(time()-86400)){
            X3::log('Error reading uploads/ dir. Just wanted to clean up captchas');
        }
        $name = 'Captcha_' . time() . rand(0, 10) . rand(100, 10000) . '.gif';
        $text = substr(str_shuffle(str_repeat('0123456789', 5)), 0, 5);
        X3::app()->user->captcha = array('text' => md5($text), 'file' => $name, 'times' => 3, 'lasttime' => time());
        $cap = new Gifcaptcha($text, 'css/fonts/myriadpro-boldcond-webfont.ttf', 'ffffff');
        $gif = $cap->AnimatedOut();
        file_put_contents('uploads/' . $name, $gif);
        echo $gif;
        exit;
    }
    
    public function actionGet() {
        $ins = $_GET['file'];
        $model = self::getByPk($ins);
        if($model == null) 
            throw new X3_404();
        $file = X3::app()->basePath . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $model->id;
        if (file_exists($file)) {
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
    
    public function cleanCaptcha($time) {
        $h = @opendir('uploads/');
        if(!$h) return false;
        while($file = @readdir($h)){
            if($file!='.' && $file!='..' && strpos($file,'Captcha_')===0){
                $ctime = filemtime("uploads/$file");
                if($ctime<=$time){
                    @unlink("uploads/$file");
                }
            }
        }
        return true;
    }
    
    public function cleanUp($model,$file) {
        if(is_object($model))
            $class = get_class ($model);
        elseif(is_string($model))
            $class = $model;
        else
            return false;
        $dir = "uploads/$class";
        $h = opendir($dir);
        while(FALSE!==($fs = readdir($h))){
            if($fs!='.' && $fs!='..' && is_dir("$dir/$fs")){
                $dir2 = "$dir/$fs";
                $h2 = opendir($dir2);
                while(FALSE!==($fs2 = readdir($h2))){
                    if($fs2 == $file && is_file("$dir2/$fs2"))
                        @unlink("$dir2/$fs2");
                }
                closedir($h2);
            }
            if($fs == $file)
                @unlink("$dir/$fs");
        }
        closedir($h);
    }

}

?>
