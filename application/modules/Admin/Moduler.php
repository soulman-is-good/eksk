<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Moduler
 *
 * @author Soul_man
 */
class Admin_Moduler extends X3_Module {
    
    public $encoding = 'UTF-8';
    
    public function init() {
        X3::app()->VIEWS_DIR = 'modules' . DIRECTORY_SEPARATOR . 'Admin';
        X3::app()->LAYOUTS_DIR = 'moduler' . DIRECTORY_SEPARATOR . 'layouts';        
        $this->template->layout = "layout";
    }
    
    public function actionIndex() {
        $this->template->render("@app:modules:Admin:moduler:index.php");
    }
}

?>
