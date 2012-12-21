<?php
$langs = array_merge(array('ru'=>'Русский'),array_intersect_key(array(
    'en'=>'Английский',
    'kz'=>'Казахский',
    'uz'=>'Узбекский',
), array_combine(X3::app()->languages, array_fill(0, count(X3::app()->languages), ''))));

?>
<style>
    .x3-lang th{
        background: #ffccff;
    }
    .x3-lang td{
        text-align: center;
    }
    .x3-lang td input{
        width:100%;
        border:none;
        text-align: center;
        font:italic 12px Verdana;
    }
</style>
<table width="100%" class="x3-lang">
    <tr>
        <?foreach($langs as $lang):?>
        <th><?=$lang?></th>
        <?endforeach;?>
    </tr>
    <?foreach ($modules as $model):?>
    <tr item-id="<?=$model->id?>">
        <td><?=$model->value?></td>
        <?foreach(X3::app()->languages as $lang): $attr = "value_$lang";?>
        <td><input<?=($model->value==$model->$attr)?' style="color:#ff0000"':''?> name="<?=$lang?>" type="text" value="<?=$model->$attr?>" original="<?=$model->$attr?>" onblur="saveit(this)" onfocus="highlight(this)" /></td>
        <?endforeach;?>
    </tr>
    <?endforeach;?>
</table>
<script>
    function highlight(el){
        $(el).parent().parent().children('td').css('background','#ccffcc')
    }
function saveit(el){
    $(el).parent().parent().children('td').css('background','none')
    var id = $(el).parent().parent().attr('item-id');
    var lang = $(el).attr('name');
    var val = $(el).val();
    if(val == $(el).attr('original')) return;
    $(el).css({'background':'url(/application/modules/Admin/images/loader_tmp.gif) no-repeat 50% 50%'})
    $.post('/lang/save',{id:id,lang:lang,value:val},function(){
        $(el).css({'background':'none'});
        if(val != $(el).attr('original')){   
            $(el).css('color','#000')
            $.growl('Сохранено','ok');
        }
        $(el).attr('original',val);
    });
}
</script>