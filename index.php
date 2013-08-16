<?php
header('Content-type: text/html; charset=utf-8');
// change the following paths if necessary
$x3=dirname(__FILE__).'/application/x3framework/X3.php';
$config=dirname(__FILE__).'/application/config/main.php';
// remove the following lines when in production mode
define('X3_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
#defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
//ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
require_once($x3);
$app = X3::init($config);
$app->run();
?>
