<?php
/**
 * Description of User_Settings
 *
 * @author Soul_man
 */
class User_Settings extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'user_settings';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
        'user_id' => array('integer[10]', 'unsigned', 'index', 'ref'=>array('User','id','default'=>'name')),
        'about' => array('content','default'=>'NULL'),
        'mobile' => array('string','default'=>''),
        'home' => array('string','default'=>''),
        'work' => array('string','default'=>''),
        'skype' => array('string','default'=>''),
        'email' => array('string','default'=>''),
        'site' => array('string','default'=>''),
    );
    
    public function onValidate($attr,$pass) {
        $pass = false;
        if($attr == 'mobile' || $attr == 'home' || $attr == 'work') {
            //TODO: phone validation
            if(preg_match("/^[0-9]{3} [0-9]{3}.[0-9]{2}.[0-9]{2}$/",$this->$attr) == false){
                $this->addError($attr,'Не корректно указан номер телефона.');
            }
        }
    }

    public function fieldNames() {
        return array(
            'about' => 'О себе',
            'mobile' => 'Мобильный',
            'home' => 'Домашний',
            'work' => 'Рабочий',
            'skype' => 'Skype',
            'email' => 'E-Mail',
            'site' => 'Веб-сайт',
        );
    }

    public function moduleTitle() {
        return 'Профиль пользователя';
    }

    public function cache() {
        return array(
            //'cache' => array('actions' => 'show', 'role' => '*', 'expire' => '+1 month'),
        );
    }

    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }

    public static function get($arr=array(), $single = false, $class = __CLASS__,$asArray=false) {
        return parent::get($arr, $single, $class,$asArray);
    }

    public static function getByPk($pk, $class = __CLASS__,$asArray=false) {
        return parent::getByPk($pk, $class,$asArray);
    }
    
    public function getLink() {
        return "/news/$this->id.html";
    }
     
    public function actionIndex() {
        $q = array('@condition'=>array('status'),'@order'=>'created_at DESC');
        $nc = News::num_rows($q);
        $pagnews = new Paginator('News', $nc);
        $nq = $q;
        $nq['@offset']=$pagnews->offset;
        $nq['@limit']=$pagnews->limit;
        $news = News::get($nq);
        SeoHelper::setMeta();
        $this->template->render('index', array('models' => $news,'paginator'=>$pagnews));
    }

    public function actionShow() {
        if (!isset($_GET['id']))
            throw new X3_404;
        $id = (int) $_GET['id'];
        $model = self::getByPk($id);
        if ($model === null)
            throw new X3_404;
        if($model->metatitle == '') $model->metatitle = $model->title;
        SeoHelper::setMeta($model->metatitle, $model->metakeywords, $model->metadescription);
        $this->template->render('show', array('model' => $model));
    }

    public function date() {
        return date('d', $this->created_at) . " " . I18n::months((int) date('m', $this->created_at)-1, I18n::DATE_MONTH) . " " . date('Y', $this->created_at);
    }
    
    public function beforeValidate() {
        if(strpos($this->created_at,'.')!==false){
            $this->created_at = strtotime($this->created_at);
        }elseif($this->created_at === 0)
            $this->created_at = time();
    }

    public function afterSave() {
        if (is_file('application/cache/news.show.' . $this->id))
            @unlink('application/cache/news.show.' . $this->id);
    }

}

?>
