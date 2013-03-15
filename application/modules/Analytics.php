<?php

class Analytics extends X3_Module {
    
    public function filter() {
        return array(
            'allow'=>array(
                'admin'=>array('index','ksk','user','vote')
            ),
            'deny'=>array(
                '*'=>array('*')
            ),
            'handle'=>'redirect:/user/login.html'
        );
    }
    
    public function actionIndex() {
        $this->template->render('index',array());
    }
    
    public function actionUser() {
        $cnt = X3::db()->fetch("SELECT COUNT(0) cnt FROM data_user WHERE role='user'");
        $paginator = new Paginator(__CLASS__.'User',(int)$cnt['cnt']);
        $models = User::get(array(
            '@condition'=>array('role'=>'user'),
            '@limit'=>$paginator->limit,
            '@offset'=>$paginator->offset
            ));
        $this->template->render('user',array('models'=>$models,'count'=>(int)$cnt['cnt'],'paginator'=>$paginator));
    }
    
    public function actionKsk() {
        $cnt = X3::db()->fetch("SELECT COUNT(0) cnt FROM data_user WHERE role='ksk'");
        $paginator = new Paginator(__CLASS__.'Ksk',(int)$cnt['cnt']);
        $models = User::get(array(
            '@condition'=>array('role'=>'ksk'),
            '@limit'=>$paginator->limit,
            '@offset'=>$paginator->offset
            ));
        $this->template->render('ksk',array('models'=>$models,'count'=>(int)$cnt['cnt'],'paginator'=>$paginator));
    }
    
    public function actionVote() {
        if(!isset($_GET['id'])){
            $cnt = X3::db()->fetch("SELECT COUNT(0) cnt FROM data_vote");
            $paginator = new Paginator(__CLASS__.'Vote',(int)$cnt['cnt']);
            $models = Vote::get(array(
                '@limit'=>$paginator->limit,
                '@offset'=>$paginator->offset,
                '@order'=>'created_at DESC'
            ));
            $this->template->render('vote',array('models'=>$models,'count'=>(int)$cnt['cnt'],'paginator'=>$paginator));
        }else{
            $id = (int)$_GET['id'];
            $model = Vote::getByPk($id);
            if($model == null)
                throw new X3_404();
            $answers = explode('||',$model->answer);
            $this->template->render('vote_show',array('model'=>$model,'answers'=>$answers));
        }
    }
}