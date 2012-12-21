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
class Photo extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_photo';
    public $_fields = array(
        'id' => array('integer[10]', 'unsigned', 'primary', 'auto_increment','orderable'),
        'image' => array('file', 'default' => 'NULL', 'allowed' => array('jpg', 'gif', 'png', 'jpeg'), 'max_size' => 10240),
        'sum' => array('string[255]', 'default' => 'NULL','unique'),
        'title' => array('string[255]', 'default' => 'NULL'),
        'status' => array('boolean', 'default' => '1','orderable'),
        'bmain' => array('boolean', 'default' => '0','orderable'),
        'created_at' => array('integer[10]', 'unsigned', 'default' => '0','orderable'),
        'catalog'=>array('integer','unused','default'=>'')
    );

    public function fieldNames() {
        return array(
            'image' => 'Фото',
            'title' => 'Подпись',
//            'bmain' => 'На главную',
            'status' => 'Видимость',
        );
    }

    public function moduleTitle() {
        return 'Фотогалерея';
    }

    public static function newInstance($class = __CLASS__) {
        return parent::newInstance($class);
    }

    public static function getInstance($class = __CLASS__) {
        return parent::getInstance($class);
    }

    public static function get($arr=array(), $single = false, $class = __CLASS__) {
        return parent::get($arr, $single, $class);
    }

    public static function getByPk($pk, $class = __CLASS__) {
        return parent::getByPk($pk, $class);
    }

    public static function delete($arr, $class = __CLASS__) {
        return parent::delete($arr, $class);
    }

    public static function deleteByPk($pk, $class = __CLASS__) {
        return parent::deleteByPk($pk, $class);
    }

    public function cache() {
        return array(
                //'cache'=>array('actions'=>'show','role'=>'*','expire'=>'+1 day'),
                //'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }
    
    public function actionIndex() {
        $q = array('@condition'=>array('status'),'@order'=>'created_at DESC');
        $count = Photo::num_rows($q);
        $pag = new Paginator(__CLASS__, $count);
        $q['@offset']=$pag->offset;
        $q['@limit']=$pag->limit;
        $models = Photo::get($q);
        SeoHelper::setMeta();
        $this->template->render('index',array('models'=>$models,'paginator'=>$pag));
    }

    public function actionDynadd() {
        if(!X3::user()->isAdmin()) throw new X3_404();
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
            $sum = md5_file($_FILES['file']['tmp_name']);
            if(NULL===($p = Photo::get(array('sum'=>$sum),1))){
                $p = new Photo();
                $fname = 'Photo-'.time();//pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
                $fext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                //$fname = X3_String::create($fname)->translit();
                $file = $fname . time() . '.' . $fext;
                if(FALSE === @move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/Photo/'.$file))
                        exit;
                $p->image = $file;
                if(!$p->save()){
                    var_dump($p->table->getErrors(),X3::db()->getErrors());
                }
            }
            echo json_encode(array('id'=>$p->id,'image'=>$p->image));
        }
        exit;
        
    }
    
    public function beforeValidate() {
        if ($this->created_at == 0)
            $this->created_at = time();
        if($this->sum==''){
            $file = 'uploads/Photo/'.$this->image;
            if(is_file($file)){
                $this->table->setAttribute('sum',md5_file($file));
            }else
                $this->table->setAttribute('sum',null);
        }
    }
    
    public function getDefaultScope() {
        if(isset($_GET['search']) && $_GET['search']!=''){
            $s = $_GET['search'];
            if(strpos($s,'#')===0){
                $s = str_replace(",", " ", $s);
                $s = preg_replace("/[\s]+/", " ", $s);
                $s = str_replace(" ", ",", trim($s));
                $s = str_replace("#", "", trim($s));
                return array('@condition'=>array('id'=>array('IN'=>"($s)")));
            }
        }
        if(isset($_GET['work']) && $_GET['work']>0){
            $w = Work::getByPk($_GET['work']);
            return array('@condition'=>array('IN'=>"($w->photos)"),'@order'=>'created_at DESC');
        }else
            return array('@order'=>'created_at DESC');
    }

}

?>
