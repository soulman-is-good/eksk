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
class Page extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_page';
    public static $parent = array();
    
    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
//        'parent_id'=>array('integer[10]','unsigned','index','default'=>'NULL','ref'=>array('Page','id','default'=>'short_title','query'=>array('@condition'=>array('parent_id'=>'NULL')))),
        'title'=>array('string[255]','language'),
        'name'=>array('string[255]','unique'),
        'text'=>array('text','language'),
        'status'=>array('boolean','default'=>'1'),
        'onmain'=>array('boolean','default'=>'0'),
        'weight'=>array('integer[10]','default'=>'0'),
        'created_at'=>array('integer[10]','unsigned','default'=>'0'),
        'metatitle'=>array('string','default'=>'','language'),
        'metakeywords'=>array('string','default'=>'','language'),
        'metadescription'=>array('string', 'default'=>'','language'),
    );

    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr,$single=false,$class=__CLASS__) {
        return parent::get($arr,$single,$class);
    }
    public static function getByPk($pk,$class=__CLASS__) {
        return parent::getByPk($pk,$class);
    }
    public function fieldNames() {
        return array(
//            'parent_id'=>'Верхний уровень',
            'title'=>'Заголовок',
            'name'=>'Имя в URL',
            'text'=>'Содержание',
            'weight'=>'Порядок',
            'status'=>'Видимость',
            'onmain'=>'На главную',
            'metatitle'=>'Metatitle',
            'metakeywords'=>'Metakeywords',
            'metadescription'=>'Metadescription',
        );
    }
    
    public function moduleTitle() {
        return 'Текстовые страницы';
    }

    public function cache() {
        return array(
            //'cache'=>array('actions'=>'show','role'=>'*','expire'=>'+1 day'),
            'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }
    
    public function getLink() {
        return "/page/$this->name.html";
    }

    public function actionShow(){
        $name = mysql_real_escape_string($_GET['name']);
        $model = $this->table->select('*')->where("`name`='$name'")->asObject(true);
        if($model==null) throw new X3_404();
        if($model->metatitle == '') $model->metatitle = $model->title;
        SeoHelper::setMeta($model->metatitle, $model->metakeywords, $model->metadescription);
        $this->template->render('show',array('model'=>$model));
    }
    
    public function parentM() {
        if(!isset(self::$parent[$this->parent_id]))
            self::$parent[$this->parent_id] = Page::getByPk($this->parent_id);
        return self::$parent[$this->parent_id];
    }
    
    public function menu() {
        /*if(isset($_GET['parent_id'])){
            $current = $_GET['parent_id'];
            $html = array('<a href="/admin/page/"><b>Все</b></a>');
        }else{
            $current = -1;
            $html = array('<span><b>Все</b></span>');
        }
        if($current == 0)
            $html []= '<span>Верхний уровень</a>';
        else
            $html []= '<a href="/admin/page/parent_id/0">Верхний уровень</a>';
        if($current>0){
            $html []= "<span>$this->short_title</span>";
        }
        return implode(' &gt; ', $html);*/
    }
    
    public function getDefaultScope() {
        $query = array('@order'=>'weight, title, created_at DESC');
        if(isset($_GET['parent_id'])){
            $p = (int)$_GET['parent_id'];
            if($p == 0)
                $query['@condition'] = array('parent_id'=>'NULL');
            else
                $query['@condition'] = array('parent_id'=>$p);
        }
        return $query;
    }
    
    public function onDelete($tables, $condition) {
        if (strpos($tables, $this->tableName) !== false) {
            $model = $this->table->select('*')->where($condition)->asObject(true);
//            X3::db()->query("UPDATE data_page SET parent_id=NULL WHERE parent_id=$model->id");
        }
        parent::onDelete($tables, $condition);
    }    

    public function beforeValidate() {
        if($this->created_at == 0)
            $this->created_at = time();
        if($this->name == ''){
            $name = new X3_String($this->title);
            $name = preg_replace("/['\"\.\/\-;:\+\)\(\*\&\^%\$#@!`]/", '', $name->translit());
            $this->name = strtolower($name);
        }
//        if($this->parent_id=="" || $this->parent_id=="0") $this->parent_id = NULL;
    }

}
?>
