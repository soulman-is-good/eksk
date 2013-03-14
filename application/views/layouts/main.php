<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow" />
<meta property="og:image" content="<?=X3::app()->baseUrl?>/images/logo.png" />
<link href="/css/style.css" type="text/css" rel="stylesheet" />
<title><?=X3::app()->name?></title>
<script type="text/javascript" src="/js/jquery.js"></script>
<?if(isset(X3::app()->datapicker)):?>
<link href="http://code.jquery.com/ui/1.10.0/themes/cupertino/jquery-ui.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<?endif;?>
<link href="/js/tipTip.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery.tipTip.js"></script>
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
    <?$uid = X3::user()->id;
    $time = time();
    $where = " WHERE 
        w.status AND
        w.end_at>=$time AND
        w.user_id<>$uid AND 
        (w.type='".strtoupper(X3::user()->role)."' OR w.type='*') AND 
        (
          (u.role='admin' AND
            (
            w.city_id IS NULL OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IN (SELECT house FROM user_address WHERE user_id=$uid) AND flat IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IN (SELECT house FROM user_address WHERE user_id=$uid) AND flat IN (SELECT flat FROM user_address WHERE user_id=$uid))
            )
          ) OR (u.role='ksk' AND (SELECT COUNT(0) FROM user_address a1, user_address a2 WHERE a1.user_id=$uid AND a2.user_id=u.id AND a1.city_id=a2.city_id AND a1.region_id=a2.region_id AND a1.house=a2.house)>0 AND
            (
            w.city_id IS NULL OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IN (SELECT house FROM user_address WHERE user_id=$uid) AND flat IS NULL) OR
            (w.city_id IN (SELECT city_id FROM user_address WHERE user_id=$uid) AND w.region_id IN (SELECT region_id FROM user_address WHERE user_id=$uid) AND w.house IN (SELECT house FROM user_address WHERE user_id=$uid) AND flat IN (SELECT flat FROM user_address WHERE user_id=$uid))
            )
          )
        )
        ";
    $ads = array(0);
    $qa = X3::db()->query("SELECT id FROM user_address WHERE user_id='$uid'");
    while($a = mysql_fetch_array($qa,MYSQL_NUM)) $ads[] = $a[0];
    if(count($ads)>1):
    $ads = implode(', ', $ads);
    //die("(SELECT w.id, w.user_id, w.created_at, w.title, NULL AS answer FROM data_warning w INNER JOIN data_user u ON u.id=w.user_id $where AND w.id NOT IN (SELECT warning_id FROM warning_stat WHERE user_id=$uid)) 
        //UNION (SELECT w.id, w.user_id, w.created_at, w.title, w.answer FROM data_vote w INNER JOIN data_user u ON u.id=w.user_id $where AND w.id NOT IN (SELECT vote_id FROM vote_stat WHERE address_id IN ($ads) GROUP BY vote_id))");
    if(!X3::user()->isGuest() && ($messages = X3::db()->query("(SELECT w.id, user_id, w.created_at, title, NULL AS answer FROM data_warning w INNER JOIN data_user u ON u.id=w.user_id $where AND w.id NOT IN (SELECT warning_id FROM warning_stat WHERE user_id=$uid)) 
        UNION (SELECT w.id, user_id, w.created_at, title, answer FROM data_vote w INNER JOIN data_user u ON u.id=w.user_id $where AND w.id NOT IN (SELECT vote_id FROM vote_stat WHERE address_id IN ($ads) GROUP BY vote_id))")) && mysql_num_rows($messages)>0):?>
    <div class="blackscreen"></div>
    <?$i=0;while($message = mysql_fetch_assoc($messages)):
        $user = X3::db()->fetch("SELECT id, CONCAT(name, ' ', surname) name, role FROM data_user WHERE id={$message['user_id']}");
        $user['name'] = $user['role']=='admin'?X3::translate('Администратор').'#'.$user['id']:$user['name'];
        ?>
    <div class="mywnd" style="<?=$i>0?'display:none;':''?>width: 640px; height: 140px; left: 50%;margin-left: -320px; top: 25%; z-index: 565;" id="warning_<?=$message['id']?>">
        <div class="eksk-wnd noresize" style="margin:0">
            <div class="head">
                <div class="buttons" style="margin-top:4px;"><?/*<a href="#close" title="Закрыть" class="close_noty"><?=X3::translate('Закрыть');?></a>*/?></div>
                <?if($message['answer']!=null):?>
                <h1><?=strtr(X3::translate('Опрос от {user}'),array("{user}"=>$user['name']));?></h1>
                <?else:?>
                <h1><?=strtr(X3::translate('Оповещение от {user}'),array("{user}"=>$user['name']));?></h1>
                <?endif;?>
            </div>
            <div class="content">
                <em><?=I18n::date($message['created_at'])?></em>
                <div style="margin-top:10px;"><?=nl2br($message['title'])?></div>
            </div>
            <div class="dialog_footer">
                    <?if($message['answer']==null):?>
                <div style="padding: 5px 5px 5px 15px; margin-left: 46px;">
                        <button class="close_noty" type="button"><?=X3::translate('Ознакомлен');?></button>
                </div>
                    <?else:
                        $answers = explode('||',$message['answer']);?>
                <div style="padding: 5px 5px 5px 15px;">
                    <?foreach($answers as $i=>$answer):?>
                        <button class="close_noty2" data-val="<?=$i?>" type="button" style="display:block;margin-bottom:10px"><?=$answer;?></button>
                    <?endforeach;?>
                </div>
                    <?endif;?>
            </div>
            <div class="shadow">&nbsp;</div>
        </div>
    </div>
    <?$i++;endwhile;?>
    <script>
        $(function(){
            $('.close_noty').click(function(){
                var p = $(this).parents('.mywnd');
                var id = p.attr('id').split('_').pop();
                $.get('/warning/read.html',{id:id},function(){})
                p.fadeOut(function(){$(this).remove();if($('.mywnd').length==0)$('.blackscreen').fadeOut(function(){$(this).remove()});else $('.mywnd').eq(0).fadeIn();})
                return false;
            })
            $('.close_noty2').click(function(){
                var p = $(this).parents('.mywnd');
                var id = p.attr('id').split('_').pop();
                $.get('/vote/read.html',{id:id,val:$(this).data('val')},function(){})
                p.fadeOut(function(){$(this).remove();if($('.mywnd').length==0)$('.blackscreen').fadeOut(function(){$(this).remove()});else $('.mywnd').eq(0).fadeIn();})
                return false;
            })
        })
    </script>
    <?endif;?>
    <?endif;//if empty $ads?>
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
                if(typeof m == 'object'){
                    for(i in m){
                        var cnt = parseInt(m[i]);
                        $('.menu_item.'+i+' b').remove();
                        if(!isNaN(cnt) && cnt>0){
                            $('.menu_item.'+i).append('<b>'+cnt+'</b>');
                            message_timeout = setTimeout(function(){message_count()}, 300000);
                        }
                    }
                }
            },'json')
        }
        $(function(){
            $('.eksk-wnd:not(.noresize)').css('min-height',$('.body').height()+'px');
            message_count();
            $('h1[title]').tipTip();
        })
    </script>
</body>
</html>