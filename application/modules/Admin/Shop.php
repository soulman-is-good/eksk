<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Shop
 *
 * @author Soul_man
 */
class Admin_Shop extends X3_Module {
    
    public function index(){
        $this->template->render('@views:admin:shop.php');
    }
}

?>
