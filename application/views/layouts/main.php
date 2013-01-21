<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta property="og:image" content="<?=X3::app()->baseUrl?>/images/logo.png" />
<link href="/css/style.css" type="text/css" rel="stylesheet" />
<title><?=X3::app()->name?></title>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.fcselect.js"></script>
<script type="text/javascript" src="/js/jquery.fctabs.js"></script>
<script type="text/javascript" src="/js/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="/js/placeholder.js"></script>
<script type="text/javascript" src="/js/wnd.js"></script>
<script type="text/javascript" src="http://api-maps.yandex.ru/2.0-stable/?lang=ru-RU&coordorder=longlat&load=package.full"></script>

<script type="text/javascript">
String.prototype.repeat = function( num ){return new Array( num + 1 ).join( this );}    
</script>
<?if(X3::user()->superAdmin):?>
<script type="text/javascript" src="/js/admin.js"></script>
    <?if(X3::app()->module->controller->action == 'edit' || X3::app()->module->controller->action == 'create'):?>
    <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
    <?include("js/sfbrowser/connectors/php/init.php");?>
    <?endif;?>
<?endif;?>

<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
</head>
<body itemscope itemtype="http://schema.org/WebPage" <?=X3::user()->isGuest()?'class="unauthorized"':''?>>
    <div class="main-block <?=X3::app()->module->controller->id." ".X3::app()->module->controller->action?>">
        <div class="header"><header>
                <?=X3_Widget::run('@layouts:header.php',array('main'=>isset($main)))?>
        </header></div>
        <div class="body">
            <?=X3_Widget::run('@layouts:menu.php',array('type'=>'Боковое'))?>
            <?=$content?>
        </div>
        <div class="footer">
            <footer>
                <?=X3_Widget::run('@layouts:footer.php',array('main'=>isset($main)));?>
            </footer>
        </div>
    </div>
    <script>
        var message_timeout = null;
        var message_count = function(){
            $.get('/message/count.html',function(m){
                m = parseInt(m);
                $('.menu_item.message b').remove();
                if(!isNaN(m) && m>0){
                    $('.menu_item.message').append('<b>'+m+'</b>');
                    message_timeout = setTimeout(function(){message_count()}, 3000);
                }
            })
        }
        $(function(){
            $('.eksk-wnd').css('min-height',$('.body').height()+'px');
            message_count();
        })
    </script>
</body>
</html>