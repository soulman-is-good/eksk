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
class Jobs extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_jobs';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),        
        'city_id'=>array('integer[10]','unsigned','index','ref'=>array('Region','id','default'=>'title','query'=>array('@condition'=>array('parent_id'=>array('IS NOT '=>"NULL")),'@order'=>'title'))),
        'sphere_id'=>array('integer[10]','unsigned','index','ref'=>array('Sphere','id','default'=>'title','query'=>array('@order'=>'title'))),
        'age'=>array('integer[2]','unsigned','default'=>'0'),
        'title'=>array('string[255]','language'),
        'content'=>array('content','language'),
        'text'=>array('text','language'),
        'status'=>array('boolean','default'=>'1'),
        'onmain'=>array('boolean','default'=>'1'),
        'created_at'=>array('datetime'),
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
    public function moduleTitle() {
        return 'Вакансии';
    }
    public function fieldNames() {
        return array(
            'city_id'=>'Город',
            'sphere_id'=>'Сфера деятельности',
            'title'=>'Название',
            'age'=>'Опыт работы (лет)',
            'content'=>'Кратко о вакансии',
            'text'=>'Содержание',
            'status'=>'Видимость',
            'onmain'=>'На главную',
            'created_at'=>'Дата публикации',
            'metatitle'=>'Metatitle',
            'metakeywords'=>'Metakeywords',
            'metadescription'=>'Metadescription',            
        );
    }
    
    public function getCity() {
        $city = Region::getByPk($this->city_id);
        if($city!=null)
            return $city->title;
        return '';
    }
    
    public function getSphere() {
        $city = Sphere::getByPk($this->sphere_id);
        if($city!=null)
            return $city->title;
        return '';
    }

    public function cache() {
        return array(
            //'cache'=>array('actions'=>'show','role'=>'*','expire'=>'+1 day'),
            'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }

    public function actionIndex(){
        $title = 'title';if(X3::app()->user->lang != 'ru') $title .= "_".X3::app()->user->lang;
        if(X3::user()->jobs == NULL)
            $this->redirect('/search/jobs.html');
        $search = X3::user()->jobs;
        $q = "";
        $attrs = array('j.title','j.title_en','j.title_kz','r.title','r.title_en','r.title_kz','s.title','s.title_en','s.title_kz','j.text','j.text_en','j.text_kz');
        if(trim($search['keywords'])!=''){
            $search['keywords'] = trim(preg_replace("/\s+/"," ",$search['keywords']));
            $t = "([ATTR] LIKE '%".implode("%' OR [ATTR] LIKE '%",explode(' ',X3::db()->validateSQL($search['keywords'])))."%')";
            $o = array();
            foreach($attrs as $attr){
                $o[]= str_replace("[ATTR]","$attr",$t);
            }
            $q .= " AND (" . implode(' OR ',$o) . ")";
        }
        if($search['city']>0){
            $q .= " AND j.city_id=".$search['city'];
        }
        if($search['sphere']>0){
            $q .= " AND j.sphere_id=".$search['sphere'];
        }
        if(trim($search['title'])!='')
            $q .= " AND j.$title='".$search['title']."'";
        $q .= " AND j.age >= ".$search['age'];
        $content = 'content';if(X3::app()->user->lang != 'ru') $content .= "_".X3::app()->user->lang;
        $query = "SELECT j.id id, j.$title title, j.$content content, j.created_at, r.$title city, s.$title sphere FROM data_jobs j INNER JOIN data_region r ON r.id=j.city_id INNER JOIN data_sphere s ON s.id=j.sphere_id WHERE
            j.status AND r.status AND s.status $q ORDER BY j.title";
        $count = X3::db()->count($query);
        $pag = new Paginator('Jobs', $count);
        $models = X3::db()->query($query . " LIMIT $pag->offset, $pag->limit");
        if(!is_resource($models)){
            throw new X3_Exception($query."<br/>\n".X3::db()->getErrors(),500);
        }
        $this->template->render('index',array('models'=>$models,'paginator'=>$pag));
    }
    
    public function actionShow() {
        if(!isset($_GET['id']) || !$_GET['id']>0 || NULL===($model = Jobs::getByPk((int)$_GET['id'])))
            throw new X3_404();
        if($model->metatitle == '')
            $model->metatitle = $model->title;
        SeoHelper::setMeta($model);
        $this->template->render('show',array('model'=>$model));
    }

    public function beforeValidate() {
        if($this->created_at == 0)
            $this->created_at = time();
    }

}
?>
