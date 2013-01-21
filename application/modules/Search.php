<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Search
 *
 * @author Soul_man
 */
class Search extends X3_Module{
    
    public function filter() {
        return array(
            'allow'=>array(
                'user'=>array('index'),
                'ksk'=>array('index'),
                'admin'=>array('index')
            ),
            'deny'=>array(
                '*'=>array('*'),
            ),
            'handle'=>'redirect:/user/login.html'
        );
    }
    
    public function actionIndex() {
        if(isset($_POST['q'])){
            X3::user()->SearchPage = 0;
            X3::user()->search = $_POST['q'];
        }
        if(($search = strip_tags(X3::user()->search['word']))!='' && ($search = trim($search))!='' && mb_strlen($search,'UTF-8')>1){
            $search = preg_replace("/[\s]+/"," ",$search);
            $search = preg_replace("/<>/","",$search);
            //keywords logic
            $query = "status";
            $type = X3::user()->search['type'];
            $title = "title" . (X3::user()->lang!='ru'?"_".X3::user()->lang:'');
            $text = "text" . (X3::user()->lang!='ru'?"_".X3::user()->lang:'');
            $attrs = array(
                'user'=>array(
                    'table'=>'data_user',
                    'default'=>"status>0 AND role='user'",
                    'fields'=>array("name","surname"),
                    'select'=>array("`id` AS link","CONCAT(name,' ',surname) AS title","NULL AS text","'user' AS `type`"),
                    'link'=>"/user/[LINK].html"
                ),
                'message'=>array(
                    'table'=>'data_message m INNER JOIN data_user u ON u.id=m.user_to',
                    'default'=>"m.user_from=".X3::user()->id,
                    'fields'=>array("content"),
                    'select'=>array("u.id AS link","IF(u.role='admin';CONCAT('Администратор#',u.id);CONCAT(u.name,' ',u.surname)) AS title","content AS text","'message' AS `type`"),
                    'link'=>"/message/with/[LINK].html"
                ),
            );
            $words = explode(' ',X3::db()->validateSQL($search));
            $t = "([ATTR] LIKE '%".implode("%' OR [ATTR] LIKE '%",$words)."%')";
            $o = array();
            $qs = array();
            $i=0;
            $ats = $attrs[$type];
            $table = $ats['table'];
            if(!empty($ats['fields'])){
                $q = $ats['default'];
                foreach($ats['fields'] as $attr){
                    $o[]= str_replace("[ATTR]","$attr",$t);
                }
                $q .= " AND (" . implode(' OR ',$o) . ")";
                $qs[0][$i] = "(SELECT COUNT(0) AS c$i FROM $table WHERE $q)";
                $qs[1][$i] = "(SELECT ".implode(',',$ats['select'])." FROM $table WHERE $q)";
                $i++;
            }
            $cnt = X3::db()->fetch("SELECT (".implode(" + ",$qs[0]).") AS cnt");
            $cnt = $cnt!==null?(int)$cnt['cnt']:0;
            $pagiator = new Paginator('Search', $cnt);
            $query = implode(" UNION ",$qs[1]) . " LIMIT $pagiator->offset, $pagiator->limit";
            $models = X3::db()->query($query);
            if(!is_resource($models)){
                throw new X3_Exception($query . "<br/>\n" . X3::db()->getErrors(),500);
            }
            $this->template->render('results',array('models'=>$models,'paginator'=>$pagiator,'data'=>$ats,'cnt'=>$cnt));
        }else{
            $this->template->render('results',array('models'=>null,'paginator'=>'','data'=>'','cnt'=>0));
        }
    }    
}

?>
