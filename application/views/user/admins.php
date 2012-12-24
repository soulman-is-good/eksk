<?php

?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" href="/admin/add.html">Добавить администратора</a></div>
        </div>
        <h1><?=X3::translate('Администраторы');?></h1>
    </div>
    <div class="content">
        <div class="stats">
            <?=X3::translate('Администраторов');?>: <?=$count?>
        </div>
        <table class="admin-list">
            <?foreach($models as $model):?>
            <tr>
                <td class="ava"><img src="/images/default.png" width="100" alt="" /></td>
                <td class="name"><a href="/user/<?=$model->id?>.html"><?=$model->name?> <?=$model->surname?></a></td>
                <td class="ops">
                    <a href="/user/send/id/<?=$model->id?>.html"><span>Написать сообщение</span></a>
                    <a href="/user/message/id/<?=$model->id?>.html"><span>Блокировать</span></a>
                    <a href="/user/delete/id/<?=$model->id?>.html"><span>Удалить</span></a>
                </td>
            </tr>
            <?endforeach;?>
        </table>
    </div>
    
</div>