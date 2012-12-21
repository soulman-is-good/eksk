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
    
    public function __construct($class=null,$attr=array()) {
        parent::__construct($class, $attr);
    }
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
}

?>
