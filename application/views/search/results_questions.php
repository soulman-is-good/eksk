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
                $model = new Vote();
                $model->acquire($u);
                ?>
            <tr>
                <td class="name"><a href="/vote/show/id/<?=$model->id?>.html"><?=nl2br(Search::highlight($model->title))?></a><br/><em><?=I18n::date($model->created_at)?> <?=date("H:i",$model->created_at)?></em></td>
            </tr>
            <?endwhile;?>
        </table>
            </td></tr>
        </table>
        <?endif;?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>