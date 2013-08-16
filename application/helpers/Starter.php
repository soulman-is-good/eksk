<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author Soul_man
 */
class Starter extends X3_Component {
    
    public $allowed = array(
        'user/login',
        'admin/add',
        'admin/deny',
        'user/deny',
        'user/add',
        'user/recover',
        'user/captcha1',
        'uploads/captcha',
        'page/*',
        'city/*',
    );
    
    private $admin = array(
        'menu/list',
        'menu/add',
        'menu/edit',
        'menu/delete',
        'sysSettings/list',
        'sysSettings/add',
        'sysSettings/edit',
        'sysSettings/delete',
    );
    
    public function init() {
        $this->addTrigger('onStartApp');
        $this->addTrigger('onRender');
        $this->addTrigger('onEndApp');
    }
    
    public function onStartApp($controller,$action) {
        if(X3::user()->isGuest() && !in_array($controller.'/*',$this->allowed) && !in_array($controller.'/'.$action,$this->allowed)){
            $controller = 'user';
            $action = 'login';
        }
        if(!X3::user()->isGuest()) {
            X3::db()->query("UPDATE data_user SET last_action=".time()." WHERE id=".X3::user()->id);
        }
//        if(X3::app()->hasComponent('mongo') && X3::mongo()!=null){
//            $time = time() - 18000; //5 minutes
//            X3::mongo()->query(array('online:remove'=>array('time'=>array('$lt'=>$time))));
//            if(!X3::user()->isGuest()){
//                X3::mongo()->query(array('online:save'=>array('_id'=>X3_Session::getInstance()->getSessionId(),'user_id'=>X3::user()->id,
//                    'time'=>time(),'ip'=>$_SERVER['REMOTE_ADDR'],'user_agent'=>$_SERVER['HTTP_USER_AGENT'],'last_action'=>$_SERVER['REQUEST_URI'])));
//            }
//        }
        $this->stopBubbling(__METHOD__);
        if(in_array($controller . "/" . $action, $this->admin)){
            $_GET['module'] = ucfirst($controller);
            return array('admin',$action);
        }
        return array($controller,$action);
    }
    
    public function onRender($output) {
        
        return $output;
    }
    
    public function onEndApp() {
        return true;
    }
    
}

?>
