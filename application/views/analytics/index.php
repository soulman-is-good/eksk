<?php
$time = time() - 2592000; // last 30 days
$login = X3::db()->count("SELECT id FROM user_stat WHERE login_at>$time");
$theme = X3::db()->count("SELECT id FROM forum_message WHERE created_at>$time");
$vote = X3::db()->count("SELECT id FROM vote_stat WHERE created_at>$time");
$users = X3::db()->count("SELECT id FROM data_user WHERE created_at>$time");
?>
<div class="eksk-wnd">
    <div class="head">
        <h1><?=X3::translate('Аналитические данные');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs="index">
            <ul>
                <li><a href="/analytics/ksk.html"><?=X3::translate('КСК');?></a></li>
                <li><a href="/analytics/user.html"><?=X3::translate('Жильцы');?></a></li>
                <li><a href="/analytics/vote.html"><?=X3::translate('Опросы');?></a></li>
                <li><a href="#index"><?=X3::translate('Активность пользователей');?></a></li>
            </ul>
            <div class="tab" id="index">
                <div class="stats" style="padding-bottom:0">
                    <b><?=X3::translate('Активность пользователей за последние 30 дней');?></b> 
                    <?/*<a href="/uploads/excel/generate/activity.xls" class="excel"><span><?=X3::translate('Экспорт в Excel');?></span></a>*/?>
                </div>
                <div class="hr">&nbsp;</div>
                <div class="admin-list mb-15">
                    <span>Количество входов в систему:</span> <b><?=X3_String::create($login)->currency()?></b>
                </div>
                <div class="admin-list mb-15">
                    <span>Количество ответов в теме:</span> <b><?=X3_String::create($theme)->currency()?></b>
                </div>
                <div class="admin-list mb-15">
                    <span>Количество голосов:</span> <b><?=X3_String::create($vote)->currency()?></b>
                </div>
                <div class="admin-list mb-15">
                    <span>Количество зарегистрировавшихся пользователей:</span> <b><?=X3_String::create($users)->currency()?></b>
                </div>
            </div>
        </div>
    </div>
    <div class="shadow">&nbsp;</div>
</div>