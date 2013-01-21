<?php
$title = $module->moduleTitle();
$pk = $module->getTable()->getPK();
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" id="send_message" href="/admin/create/module/<?=$class?>.html">Добавить</a></div>
        </div>
        <h1><?=$title?></h1>
    </div>
    <div class="content">
        <div class="admin-list">
            <?foreach($models as $model):?>
                <div class="message_block" style="height:auto;min-height: 0">
                    <div class="inside_block">
                        <div class="middle_side" style="width:auto">
                                <a href="/admin/view/module/<?=$class?>/id/<?=$model[$pk]?>.html"><?=isset($model['title'])?$model['title']:(isset($model['name'])?$model['name']:$model[$pk])?></a>
                        </div>
                        <div class="right_side" style="float:right;position:relative;top:-8px;text-align: right;width:250px;">
                            <div class="wrapper"><a href="/admin/edit/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-rb no-rt">Редактировать</a>
                            <a href="/admin/delete/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-lt no-lb">Удалить</a></div>
                        </div>
                    </div>
                </div>
            <?endforeach;?>
        </div>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>