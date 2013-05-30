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
class AdminSide extends X3_Component {
    
    public static $theme = '.kansha';
    
    public function init() {
        $this->addTrigger('onStartApp');
        $this->addTrigger('onRender');
    }
    
    public function onStartApp($controller,$action) {
        //For profileing
        //X3::profile()->enable = X3::user()->isAdmin();
        if($controller == 'admin'){
            X3::app()->VIEWS_DIR = 'modules' . DIRECTORY_SEPARATOR . 'Admin';
            X3::app()->LAYOUTS_DIR = 'admin' . DIRECTORY_SEPARATOR . 'layouts';
            //Assets::publish();
            //X3::app()->MODULES_DIR = 'modules' . DIRECTORY_SEPARATOR . 'Admin';
            
            //$this->stopBubbling(__FUNCTION__);
        }
        return array($controller,$action);
    }
    
    public function onRender($output) {
        
        return $output;
    }
    
}

?>
