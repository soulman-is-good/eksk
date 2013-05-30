<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paginator
 *
 * @author Soul_man
 */
class Paginator extends X3_Renderer {

    public $model = null;
    public $count = 0;
    public $page = 0;
    public $pages = 0;
    public $limit = 0;
    public $offset = 0;
    public $url = '';
    public $radius = 10;

    public function __construct($model, $count, $modelTitle=null,$generateSettings = true) {
        $this->model = $model;
        $this->count = $count;
        $lim = $model."Limit";
        $slim = $model.".Limit";
        $pag = $model."Page";
        if($modelTitle == null)
            if(class_exists($model))
                $modelTitle = X3_Module_Table::getInstance($model)->moduleTitle();
            else
                $modelTitle = $model;
        $limit = X3::app()->user->$lim;
        if($limit == null) 
            if($generateSettings)
                $limit = (int)SysSettings::getValue($slim, 'integer', "Количество `$modelTitle` на страницу", 'Навигация',10);
            else
                $limit = 20;
        $this->limit = $limit;
        $this->pages = ceil($this->count / $limit);
        if(isset($_GET['page'])){
            $page = (int)$_GET['page'] - 1;
            if($page<0 || $page>$this->pages) $page = 0;
            X3::app()->user->$pag = $page;
        }else
            $page = X3::app()->user->$pag;
        if($page == null) $page = 0;
        $this->page = $page;

        $this->offset = $this->limit * $this->page;
        $uri = explode('/',X3::app()->request->url);
        //$uri = X3::app()->request->uri;
        $i = array_search('page', $uri);
        if($i!==false){
            unset($uri[$i+1],$uri[$i]);
            $this->url = implode('/',$uri);
        }else
            $this->url = X3::app()->request->url;
    }

    public function query() {
        $from = $this->page * $this->limit;
        return " LIMIT $from, $this->limit";

    }

    public function  __toString() {
        if($this->pages>1)
            return $this->renderPartial('@layouts:paginator.php',array('P'=>$this),true);
        return "";
    }
}
?>
