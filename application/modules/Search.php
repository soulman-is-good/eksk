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
    
    /**
     * @throws X3_Exception
     */
    public function actionIndex() {
        if(isset($_POST['q'])){
            X3::user()->SearchPage = 0;
            X3::user()->search = $_POST['q'];
        }
        if(($search = strip_tags(X3::user()->search['word']))!='' && ($search = trim($search))!='' && mb_strlen($search,'UTF-8')>1){
            $search = preg_replace("/[\s]+/"," ",$search);
            $search = preg_replace("/<>/","",$search);
            $type = strtolower(X3::user()->search['type']);
            $class = false;
            $query = false;
            switch($type){
                case 'user':
                    $class = 'User';
                    $query = User::search($search,'user');
                break;
                case 'notify':
                    $class = 'Warning';
                    $query = Warning::search($search);
                break;
                case 'ksk':
                    if(X3::user()->isAdmin()){
                        $class = 'User';
                        $query = User::search($search,'ksk');
                    }
                break;
                case 'message':
                    $class = 'Message';
                    $query = Message::search($search);
                break;
                case 'themes':
                    $class = 'Forum';
                    $query = Forum::search($search);
                break;
                case 'reports':
                    $class = 'User_Report';
                    $query = User_Report::search($search);
                break;
                case 'questions':
                    $class = 'Vote';
                    $query = Vote::search($search);
                break;
            }
            if(!$class || !$query)
                throw new X3_Exception("Illegal search type",403);
            $tname = X3_Module_Table::getInstance($class)->tableName;
            $qC = new X3_MySQL_Query($tname);
            $queryNum = $query;
            $cnt = X3::db()->count($qC->formQuery($queryNum)->buildSQL());
            $pagiator = new Paginator('Search', $cnt);
            $query['@limit'] = $pagiator->limit;
            $query['@offset'] = $pagiator->offset;
//            echo $qC->formQuery($query)->buildSQL();exit;
            $models = X3::db()->query($qC->formQuery($query)->buildSQL());
            if(!is_resource($models))
                throw new X3_Exception("Database error while quering".X3::db()->lastQuery().".<br/> ".X3::db()->getErrors(),500);
            $this->template->render("results_$type",array('models'=>$models,'paginator'=>$pagiator,'cnt'=>$cnt));
        }else{
            $this->template->render('results',array('models'=>null,'paginator'=>'','data'=>'','cnt'=>0));
        }
    }    
    
    public static function highlight($str) {
        $word = X3::user()->search['word'];
        $len = mb_strlen($word,X3::app()->encoding);
        $strlen = mb_strlen($str,X3::app()->encoding);
        $ic = 256 - $len;
        $res = "";
        $k = mb_stripos($str,$word,0,X3::app()->encoding);
        $res = ($k<$ic?"":"...") . mb_substr($str, $k - $ic,$len+$ic,X3::app()->encoding) . ($len+$ic>$strlen?"":"...");
        $res = str_ireplace("$word","<b style='color:#933'>$word</b>",$res);
    }
}

?>
