<?php
$limit = min(array($paginator->limit,$models?mysql_num_rows($models):0));
$limit = ceil($limit/2);
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
            <?=X3::translate('Ничего не найдено');?>
        </div>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>