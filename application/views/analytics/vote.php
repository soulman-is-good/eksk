<?php
$types = array(
    '*'=>'',
    'user'=>'пользователям',
    'admin'=>'администраторам',
    'ksk'=>'КСК',
);
?>
<div class="eksk-wnd">
    <div class="head">
        <h1><?=X3::translate('Аналитические данные');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs="vote">
            <ul>
                <li><a href="/analytics/ksk.html"><?=X3::translate('КСК');?></a></li>
                <li><a href="/analytics/user.html"><?=X3::translate('Жильцы');?></a></li>
                <li><a href="#vote"><?=X3::translate('Опросы');?></a></li>
                <li><a href="/analytics.html"><?=X3::translate('Активность пользователей');?></a></li>
            </ul>
            <div class="tab" id="vote">
                <div class="stats">
                    <b><?=X3::translate('Опросов');?>: <?=$count?></b> <a href="/uploads/excel/generate/vote.xls" class="excel"><span><?=X3::translate('Экспорт в Excel');?></span></a>
                </div>
                <table class="admin-list">
                    <?foreach($models as $model):?>
                    <tr>
                        <td class="text">
                            <p><a href="/analytics/vote/id/<?=$model->id?>.html"><?=nl2br($model->title);?></a></p>
                            <em><?=I18n::date($model->created_at)?></em>
                            <em>
                            , <?=X3::translate('Всем '.$types[strtolower($model->type)])?>
                                <?=$model->city_id>0?X3::translate(strtr('в {city}',array('{city}'=>City::getByPk($model->city_id)->title))):''?>
                                <?=$model->region_id>0?', '.City_Region::getByPk($model->region_id)->title:''?>
                                <?=$model->house>0?', '.X3::translate(strtr('дом {house}',array('{house}'=>$model->house))):''?>
                                <?=$model->flat>0?', '.X3::translate(strtr('кв. {flat}',array('{flat}'=>$model->flat))):''?>
                            </em>
                        </td>
                    </tr>
                    <?endforeach;?>
                </table>
            </div>
        </div>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow">&nbsp;</div>
</div>