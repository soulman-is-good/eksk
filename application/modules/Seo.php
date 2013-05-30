<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Seo
 *
 * @author Soul_man
 */
class Seo extends X3_Module{

    public static function newInstance($class=null) {
        return parent::newInstance(__CLASS__);
    }
    public static function getInstance($class=null) {
        return parent::getInstance(__CLASS__);
    }
    
    public function actionIndex() {
        throw new X3_404();
    }
    
    public function actionSave() {        
        if(!IS_AJAX) throw new X3_404();
        $this->template->layout = null;
        $post = $_POST;
        $error = false;
        //X3::app()->db->startTransaction();
        $msg = array('<b>Сохранено!</b>');
        foreach($post as $module=>$fields){
            foreach($fields as $id=>$field){
                $model = X3_Module::getInstance($module)->table->select('*')->where('id='.$field['id'])->asObject(true);
                if($module=='SysSettings'){
                    $model->value = $field['value'];
                }else{
                    $msg[$module] = "<i>".SeoHelper::$labels[$module]."</i>";
                    $model->metatitle = $field['metatitle'];
                    $model->metakeywords = $field['metakeywords'];
                    $model->metadescription = $field['metadescription'];
                }
                foreach(X3::app()->languages as $lang){
                    if($module=='SysSettings'){
                        $l = "value_$lang";
                        $model->$l = $field[$l];
                    }else{
                        $l = "metatitle_$lang";
                        $model->$l = $field[$l];
                        $l = "metakeywords_$lang";                        
                        $model->$l = $field[$l];
                        $l = "metadescription_$lang"; 
                        $model->$l = $field[$l];
                    }
                }                
                if(!$model->save()){
                    $error = "$module=>".print_r($model->table->getErrors(),1);
                }
            }
        }
        if($error){//!X3::app()->db->commit()){
            echo json_encode(array('message'=>$error,'status'=>'ERROR'));
            //X3::app()->db->rollback();        
        }else
            echo json_encode(array('message'=>implode("<br/>",$msg),'status'=>'OK'));
        exit;
    }
    
}

?>
