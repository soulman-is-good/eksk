<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta property="og:image" content="<?=X3::app()->baseUrl?>/images/logo.png" />
<link href="/css/style.css" type="text/css" rel="stylesheet" />
<title><?=X3::app()->name?></title>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.fcselect.js"></script>
<script type="text/javascript" src="/js/placeholder.js"></script>
<script type="text/javascript" src="/js/wnd.js"></script>

<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
</head>
<body itemscope itemtype="http://schema.org/WebPage">
    <div class="main-block <?=X3::app()->module->controller->id." ".X3::app()->module->controller->action?>">
        <div class="header"><header>
                <?=X3_Widget::run('@layouts:header.php',array('main'=>isset($main)))?>
        </header></div>
        <div class="body">
            <?=X3_Widget::run('@layouts:menu.php',array('type'=>'Боковое'))?>
            <?=$content?>
            <div class="shadow"><i></i><b></b><em></em></div>
        </div>
        <div class="footer">
            <footer>
                <?=X3_Widget::run('@layouts:footer.php',array('main'=>isset($main)));?>
            </footer>
        </div>
    </div>
</body>
</html>