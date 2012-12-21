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
    
    public function init() {
        $this->addTrigger('onStartApp');
        $this->addTrigger('onRender');
        $this->addTrigger('onEndApp');
    }
    
    public function onStartApp($controller,$action) {
        if(X3::user()->isGuest() && $controller!='user' && $action!='login'){
            $controller = 'user';
            $action = 'login';
        }
        $this->stopBubbling(__METHOD__);
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
