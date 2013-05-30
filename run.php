<?php
// change the following paths if necessary
$x3=dirname(__FILE__).'/../../x3framework2.0/X3.php';
$config=dirname(__FILE__).'/application/config/console.php';
// remove the following lines when in production mode
define('X3_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
#defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
ini_set('display_errors', true);
error_reporting(E_ALL);
require_once($x3);
$app = X3::console($config);
$app->run();
?>