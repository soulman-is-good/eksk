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
class Promo extends X3_Module_Table {

    public $encoding = 'UTF-8';
    /*
     * uncomment if want new model functional
     */
    public $tableName = 'data_promo';

    public $_fields = array(
        'id'=>array('integer[10]','unsigned','primary','auto_increment'),
        'page_id'=>array('integer[10]','unsigned','default'=>'NULL','index','ref'=>array('Page','id','default'=>'title')),
        'image'=>array('file','allowed'=>array('jpg','gif','png','jpeg'),'max_size'=>10240000),
        'title'=>array('string[255]','default'=>'NULL','language'),
        'text'=>array('content','default'=>'NULL','language'),
        'link'=>array('string[255]','default'=>'NULL'),
        'status'=>array('boolean','default'=>'1'),
        'created_at'=>array('datetime')
    );

    public function fieldNames() {
        return array(
            'image'=>'Фото',
            'page_id'=>'Привязка к текстовой странице',
            'title'=>'Название',
            'link'=>'Ссылка',
            'text'=>'Содержание',
            'status'=>'Видимость',
        );
    }
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
    public function cache() {
        return array(
            //'cache'=>array('actions'=>'show','role'=>'*','expire'=>'+1 day'),
            //'nocache'=>array('actions'=>'*','role'=>'admin')
        );
    }
    
    public function realTitle() {
        if($this->title != '')
            return $this->title;
        if($this->page_id>0){
            $page = Page::getByPk($this->page_id);
            if($page != null)
                return $page->title;
        }
        return $this->title;
    }
    
    public static function getNormalized($params,$single=false,$asArray=false) {
        $models = self::get($params,$single);
        if($single){
            if($models->page_id>0){
                $page = Page::getByPk($models->page_id);
                if($page!==null){
                    if($models->title == '')
                        $models->title = $page->title;
                    if($models->link == '')
                        $models->link = "/page/$page->name.html";
                    if(trim(strip_tags($models->text)) == '')
                        $models->text = X3_String::create(strip_tags($page->text))->carefullCut(520);
                }
                if($asArray)
                    return $models->toArray();
                return $models;
            }
        }
        foreach($models as $model)
            if($model->page_id>0){
                $page = Page::getByPk($model->page_id);
                if($page!==null){
                    if($model->title == '')
                        $model->title = $page->title;
                    if($model->link == '')
                        $model->link = "/page/$page->name.html";
                    if(trim(strip_tags($model->text)) == '')
                        $model->text = (string)X3_String::create(strip_tags($page->text))->carefullCut(520);
                }
            }
        if($asArray){
            if(!$single && $models->count() == 1)
                return array($models->toArray());
            return $models->toArray();
        }
        return $models;
    }
    
    public function beforeValidate() {
        if(strpos($this->created_at,'.')!==false){
            $this->created_at = strtotime($this->created_at);
        }elseif($this->created_at == 0)
            $this->created_at = time();
    }

}
?>
