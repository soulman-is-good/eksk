<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?= X3::app()->name; ?></title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <link rel ="stylesheet" type= "text/css" href ="/application/modules/Admin/css/instinct/jquery-ui-1.8.19.custom.css" />        
        <link rel ="stylesheet" type= "text/css" href ="/application/modules/Admin/css/ui.jqgrid.css" />        
        <link rel ="stylesheet" type= "text/css" href ="/application/modules/Admin/css/style<?=AdminSide::$theme?>.css" />        
        <script src="/js/jquery.js"></script>
        <script src="/application/modules/Admin/js/admin.js"></script>
        <script src="/application/modules/Admin/js/jquery-ui-1.8.19.custom.min.js"></script>
        <script src="/application/modules/Admin/js/jquery.jqGrid.min.js"></script>
        <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <?include("js/sfbrowser/connectors/php/init.php");?>
    </head>
    <body>
        <div x3-layout="menu" class="x3-menu"><?=$this->renderPartial("@views:admin:menu.php")?></div>
        <div class="admin-body">            
            <div x3-layout="content" class="x3-content">
                <?=$content?>
            </div>
        </div>
        
        <script type="text/javascript">
            $(document).ready(function(){
                $('button').each(function(){
                    $(this).button({icons:{primary:$(this).attr('primary-icon'),secondary:$(this).attr('secondary-icon')}});
                })
                <?if(NULL!==($err = X3_Session::readOnce('error'))):
                    if(!is_array($err)) $err = array($err);foreach($err as $er):?>
                $.growl('<?=addslashes($er)?>');
                <?endforeach;endif;?>
                <?if(NULL!==($err = X3_Session::readOnce('success'))):
                    if(!is_array($err)) $err = array($err);foreach($err as $er):?>
                $.growl('<?=addslashes($er)?>','ok');
                <?endforeach;endif;?>
            })
        </script>
    </body>
</html>

