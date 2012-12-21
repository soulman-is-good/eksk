<style>
    #x3-container tr:nth-child(odd) td{
        background: #fbe8ff;
    }
</style>
<?php/*
$works = X3::db()->fetchAll("SELECT id, title FROM data_work ORDER BY weight");
?>
<div class="x3-submenu">
    <?
    $url = X3::app()->request->uri;
    foreach($url as $i=>$u){
        if($u == 'work'){
            unset($url[$i]);
            unset($url[$i+1]);
            break;
        }
    }
    $url = implode('/',$url);?>
    <?if(isset($_GET['work']) && $_GET['work']==0):?>
        <span>Все</span> :: 
    <?else:?>
        <a href="/<?=$url."/work/0"?>">Все</a> :: 
    <?endif;
    $c = count($works);
    foreach ($works as $i=>$work):?>
    <?if(isset($_GET['work']) && $_GET['work']==$work['id']):?>
    <span><?=$work['title']?></span><?if($c>$i+1) echo' :: ';?>
    <?else:?>
    <a href="/<?=$url."/work/".$work['id']?>"><?=$work['title']?></a><?if($c>$i+1) echo' :: ';?>
    <?endif;?>
    <?endforeach;?>
</div>*/?>
<?=
$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>array(
                'id'=>'#',//array('@value'=>'Дата добавления','@wrapper'=>'<i style="color:gray;font-size:11px">{{=date("d.m.Y H:i:s",{@created_at})}}</i>'),
                'created_at'=>array('@value'=>'Дата добавления','@wrapper'=>'<i style="color:gray;font-size:11px">{{=date("d.m.Y H:i:s",{@created_at})}}</i>'),
                'image'=>array('@value'=>'Изображение','@wrapper'=>'<a href="#" onclick="show_dialog(\'/uploads/Photo/{@image}\');return false"><img src="/uploads/Photo/64x64xh/{@image}" /></a>'),
                'status'=>'Видимость',
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''
            )
        )?>
<script type="text/javascript">
    function show_dialog(img){
        var i = new Image();
        $(i).load(function(){
            var t = ($(window).height() - i.height)/2;
            if(t<0) t = 10;
            var l = ($(window).width() - i.width)/2;
            $('<img src="'+img+'" />').dialog({modal:true,left:l,top:t,width:i.width,height:i.height,title:img});
        }).attr('src',img);
    }
</script>