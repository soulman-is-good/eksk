<?php
return array(
    'basePath'=>dirname(dirname(dirname(__FILE__))),
    'baseUrl'=>'http://maggroup.kz',
    'name'=>'Mag group',

    'locale'=>'ru',
    'languages'=>array('en','kz'),
    'components'=>array(
        'db'=>array(
            'host'=>'localhost',
            'user'=>'root',
            'password'=>'ghj,bhrf2011',
            'database'=>'kansha_tmp'
        ),
        'log'=>array(
            'dblog'=>array(
                'class'=>'X3_Log_File',
                'directory'=>'@app:log',
                'filename'=>'mysql-{d-m-Y}.log',
                'category'=>'db'
            ),
            'applog'=>array(
                'class'=>'X3_Log_File',
                'directory'=>'@app:log',
                'filename'=>'application-{d-m-Y}.log',
                'category'=>'application'
            ),
            'mailer'=>array(
                'class'=>'X3_Log_File',
                'directory'=>'@app:log',
                'filename'=>'mailer-{d-m-Y}.log',
                'category'=>'mailer'
            ),
        )
    )
);
?>

