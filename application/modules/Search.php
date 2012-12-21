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
class Search extends X3_Module_Table{
    
    public $encoding = 'UTF-8';
    public $tableName = 'data_search';
    
    public $_fields = array(
            'id'=>array('integer[11]','unsigned','primary','auto_increment'),
            'ip'=>array('string[32]'),
            'keyword'=>array('string[255]','index'),
            'count'=>array('integer','default'=>'1')
    );
    
    
    public function actionIndex() {
        if(isset($_GET['q'])){
            X3::user()->SearchPage = 0;
            X3::user()->search = $_GET['q'];
            $this->redirect('/search/results.html');
        }
        $this->template->render('index');
    }
    public function actionResults() {
        if(($search = strip_tags(X3::user()->search))!='' && ($search = trim($search))!='' && mb_strlen($search,$this->encoding)>2){
            $search = preg_replace("/[\s]+/"," ",$search);
            $search = preg_replace("/<>/","",$search);
            //keywords logic
            $words = explode(' ', $search);
            X3::db()->query("LOCK TABLES data_search WRITE");
            foreach($words as $word){
                $tmp = mysql_real_escape_string($word);
                $s = X3::db()->fetch("SELECT id,count FROM data_search WHERE keyword LIKE '$tmp'");
                if($s){ if($s['ip'] != $_SERVER['REMOTE_ADDR'])
                    X3::db()->query("UPDATE data_search SET count=".($s['count']+1)." WHERE id=".$s['id']);
                }else{
                    $s = new self;
                    $s->ip = $_SERVER['REMOTE_ADDR'];
                    $s->keyword = $word;
                    $s->count = 1;
                    if(!$s->save()){
                        var_dump($s->getTable()->getErrors());exit;
                    }
                }
            }
            X3::db()->query("UNLOCK TABLES");
            $query = "status";
            $title = "title" . (X3::user()->lang!='ru'?"_".X3::user()->lang:'');
            $text = "text" . (X3::user()->lang!='ru'?"_".X3::user()->lang:'');
            $attrs = array(
                'data_news'=>array(
                    'default'=>'status',
                    'fields'=>array("$title","$text"),
                    'select'=>array("id AS link","$title AS title","$text AS text","'data_page' AS `type`"),
                    'link'=>"/news/[LINK].html"
                ),
                'data_page'=>array(
                    'default'=>'status',
                    'fields'=>array("$title","$text"),
                    'select'=>array("`name` AS link","$title AS title","$text AS text","'data_page' AS `type`"),
                    'link'=>"/page/[LINK].html"
                ),
            );
            $t = "([ATTR] LIKE '%".implode("%' OR [ATTR] LIKE '%",explode(' ',X3::db()->validateSQL($search)))."%')";
            $o = array();
            $qs = array();
            $i=0;
            foreach($attrs as $table=>$ats){
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
            $this->template->render('results',array('models'=>$models,'paginator'=>$pagiator,'data'=>$attrs,'cnt'=>$cnt));
        }else{
            $this->redirect('/search.html');
        }
    }
    
    
    public function actionJobs() {
        if(isset($_POST['q'])){
            X3::user()->JobsPage = 0;
            X3::user()->jobs = $_POST['q'];
            $this->redirect('/jobs.html');
        }
        $cities = X3::db()->query("SELECT r.id, r.$title title FROM data_region r INNER JOIN data_jobs j ON j.city_id=r.id WHERE r.status AND j.status GROUP BY r.id ORDER BY r.title ");
        if(!is_resource($cities))
            throw new X3_Exception(X3::db()->getErrors(),500);
        $spheres = X3::db()->query("SELECT r.id, r.$title title FROM data_sphere r INNER JOIN data_jobs j ON j.sphere_id=r.id WHERE r.status AND j.status GROUP BY r.id ORDER BY r.title ");
        if(!is_resource($spheres))
            throw new X3_Exception(X3::db()->getErrors(),500);
        $titles = X3::db()->query("SELECT j.$title title, j.age age FROM data_jobs j INNER JOIN data_sphere s ON j.sphere_id=s.id INNER JOIN data_region r ON r.id=j.city_id 
            WHERE r.status AND s.status AND j.status ORDER BY r.title ");
        if(!is_resource($titles))
            throw new X3_Exception(X3::db()->getErrors(),500);
        $this->template->render('@views:jobs:search.php',array('cities'=>$cities,'spheres'=>$spheres,'titles'=>$titles));
    }
    
}

?>
