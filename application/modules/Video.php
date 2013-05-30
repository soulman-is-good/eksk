<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Section is linked table to data_catalog. Groups catalog entities to a sections
 *
 * @author Soul_man
 */
class Video extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_video';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'preview'=>array('file','default'=>'NULL','fromurl','allowed'=>array('jpg','gif','png','jpeg'),'max_size'=>10240),
        'title'=>array('string[255]','language'),
        'code'=>array('html'),
        'status'=>array('boolean','default'=>'1'),
        'tomain'=>array('boolean','default'=>'1'),
        'created_at'=>array('datetime'),
        'metatitle' => array('string', 'default' => '', 'language'),
        'metakeywords' => array('string', 'default' => '', 'language'),
        'metadescription' => array('string', 'default' => '', 'language'),        
    );

    public function fieldNames() {
        return array(
            'title'=>'Название',
            'code'=>'Код ролика',
            'preview'=>'Превью',
            'status'=>'Видимость',
            'tomain'=>'На главную',
            'created_at'=>'Дата создания',
            'metatitle'=>'Metatitle',
            'metakeywords'=>'Metakeywords',
            'metadescription'=>'Metadescription',            
        );
    }
    public static function newInstance($class=__CLASS__) {
        return parent::newInstance($class);
    }
    public static function getInstance($class=__CLASS__) {
        return parent::getInstance($class);
    }
    public static function get($arr=array(),$single=false,$class=__CLASS__,$asArray=false) {
        return parent::get($arr,$single,$class,$asArray);
    }
    public static function getByPk($pk,$class=__CLASS__,$asArray=false) {
        return parent::getByPk($pk,$class,$asArray);
    }
    
    public function getLink() {
        return "/video/$this->id.html";
    }
    
    public function actionIndex(){
        $count = self::num_rows(array('status'));
        $nq = array('@condition'=>array('status'),'@order'=>'created_at DESC');
        $pag = new Paginator(__CLASS__, $count);
        $nq['@offset']=$pag->offset;
        $nq['@limit']=$pag->limit;        
        $models = self::get($nq);
        SeoHelper::setMeta();
        $this->template->render('index',array('models'=>$models,'paginator'=>$pag));
    }
    
    public function actionShow(){
        $id = (int)($_GET['id']);
        if(!$id>0) throw new X3_404();
        $model = self::getByPk($id);
        if($model==null) throw new X3_404();
        if($model->metatitle=='')$model->metatitle=$model->title;
        SeoHelper::setMeta($model->metatitle, $model->metakeywords, $model->metadescription);
        $this->template->render('show',array('model'=>$model));
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

}
?>
