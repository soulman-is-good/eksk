<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of iForm
 *
 * @author Soul_man
 */
class Form extends X3_Form {
    
    public $defaultWrapper = array(
        'row'=>"<tr><td class=\"label\"><label for=\"%Id\">%label</label></td><td class=\"field\"><div class=\"wrapperEx inline-block\">%field</div></td><td>%required</td></tr>",
        'wraper'=>"<table>%rows<tr><td colspan=\"3\">%submit</td></tr></table>"
    );
    
    public $defaultScripts = array(
        'text'=>"<script>
                    if(typeof CKEDITOR.instances['%Id'] != 'undefined')
                        delete(CKEDITOR.instances['%Id']);
                    CKEDITOR.replace( '%Id' );
                </script>",
        'datetime'=>"<script>
            $('#%Id').datepicker({'dateFormat':'dd.mm.yy',
            'dayNamesMin':['Вс.','Пн.','Вт.','Ср.','Чт.','Пт.','Сб.'],
            'monthNames':['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь']
            })
            </script>"        
    );
    
    public function __construct($class=null,$attr=array()) {
        parent::__construct($class, $attr);
    }
    
    public function error($param) {
        if($this->module instanceof X3_Module){
            $errors = $this->module->getTable()->getErrors();
            $errors = $errors[$param];
            if(!empty($errors)){
                return array_shift($errors);
            }
        }
        return false;
    }
}

?>
