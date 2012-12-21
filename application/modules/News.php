<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of News
 *
 * @author Soul_man
 */
class News extends X3_Module_Table {

    public $encoding = 'UTF-8';
    public $tableName = 'data_news';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment'),
//        'image' => array('file', 'default' => 'NULL', 'allowed' => array('jpg', 'gif', 'png', 'jpeg'), 'max_size' => 10240),
        'title' => array('string[255]', 'language'),
        'content' => array('content', 'language'),
        'text' => array('text', 'language'),
        'status' => array('boolean', 'default' => '1'),
        'onmain' => array('boolean', 'default' => '1'),
        'created_at' => array('datetime', 'default' => '0'),
        'metatitle' => array('string', 'default' => '', 'language'),
        'metakeywords' => array('string', 'default' => '', 'language'),
        'metadescription' => array('string', 'default' => '', 'language'),
    );

    public function fieldNames() {
        return array(
            'created_at' => 'Дата',
//            'image' => 'Картинка',
            'title' => 'Заголовок',
            'content' => 'Краткое содержание',
            'text' => 'Полное содержание',
            'status' => 'Видимость',
            'onmain' => 'На главную',
            'metatitle'=>'Metatitle',
            'metakeywords'=>'Metakeywords',
            'metadescription'=>'Metadescription',
        );
    }

    public function moduleTitle() {
        return 'Новости';
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
