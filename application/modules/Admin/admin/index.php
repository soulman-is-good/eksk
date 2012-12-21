<style>
    .x3-informer{
        width:400px;
        height:300px;
        border: 1px solid #330033;
        margin:5px;
        float:left;
    }
    .x3-informer h3 {
        margin:0;
        padding: 5px 0;
        text-align: center;
        color:#330033;
        border-bottom: 1px solid #330033;
        font-size: 16px;
        height: 20px;
    }
    .x3-informer .informer-content{
        height: 270px;
        overflow: auto;        
    }
    .informer-content .inf{
        margin: 5px;
        border-bottom: 1px dotted #ccccff;
    }
</style>
<div style="margin-left:10px;">
<h1>Добро пожаловать!</h1>
<?php
$noinfo = true;
$informers = Informer::get(array('@condition'=>array('category'=>'new_property','status'=>'0'),'@order'=>'created_at DESC','@limit'=>50));
if($informers->count()>0):
$noinfo = false;
?>
<div class="x3-informer">
    <h3>Новые свойства <a onclick="clearall('new_property')" href="#clear" title="Очистить" style="float:right;margin-right: 10px" class="ui-widget-content ui-corner-all ui-icon ui-icon-trash">&nbsp;</a></h3>
    <div class="informer-content new_property-content">
        <?foreach($informers as $inf):?>
        <div class="inf"><?=$inf->message?></div>
        <?endforeach;?>
    </div>
</div>
<?endif;?>
<div style="clear:both;"><!----></div>
<?if($noinfo):?>
<i>Пока нет никакой информации</i>
<? endif; ?>
</div>
<script type="text/javascript">
    function clearall(cat){
        $.get('/informer/clearall/cat/'+cat,function(){
            $('.'+cat+'-content').html('');
        });
    }
</script>