<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?= X3::app()->name; ?></title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <script src="/js/jquery.js"></script>
	<link rel ="stylesheet" type= "text/css" href ="/application/modules/Admin/css/style.css" />        
    </head>
    <body>
<div id="splash">
        <div class="login<?=isset($_POST['password'])?' load':''?>">           
            <a href="http://www.zuber.kz" target="_blank"><img alt="ZUBER" src="/images/zuber.png" /></a>
            <form name="X3.CMS" id="X3.CMS" method="post">
                <input style="text-align:center" value="" name="password" type="password" /><br/>
                <button type="submit">Войти</button>
            </form>
        </div>    
</div>
<div id="poweredby"><a href="http://code.google.com/p/x3framework/"><img width="128" src="/images/poweredby.png" /></a></div>
        <script>
            $(function(){
                $('.login').addClass('load');
            })
        </script>
    </body>
</html>
    