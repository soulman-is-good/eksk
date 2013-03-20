<?php
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <?/*<div class="wrapper inline-block"><a class="button inline-block" id="add_admin" href="#admin/add.html"><?=X3::translate('Написать сообщение')?></a></div>*/?>
        </div>
        <h1><?=X3::translate('Результаты поиска');?></h1>
    </div>
    <div class="content">
        <div class="stats"><em>
            <?if($cnt>0):?>
            <?=X3::translate('Найдено');?>: <?=$cnt?>
            <?else:?>
            <?=X3::translate('Ничего не найдено');?>
            <?endif;?></em>
        </div>
        <?if($cnt>0):?>
        <table width="100%" class="search-results">
            <tr><td width="100%" class="fc">
        <table class="admin-list">
            <?$i=1;while($u = mysql_fetch_assoc($models)):
                $model = new Warning();
                $model->acquire($u);
                ?>
            <tr>
                <td class="name"><a href="#" class="crybaby"><?=Search::highlight($model->title)?></a></td>
            </tr>
            <?endwhile;?>
        </table>
            </td></tr>
        </table>
        <?endif;?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $('.crybaby').each(function(){$(this).click(function(){
            $.dialog($('<div class="m-10"></div>').append($(this).text()),'Оповещение', {callback:function(){return true},caption:'Закрыть'});
            return false;
    })})
</script>