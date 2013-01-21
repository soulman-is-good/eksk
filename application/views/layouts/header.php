<?php
$srch = array(
    'user'=>X3::translate('Жильцов'),
    //'ksk'=>X3::translate('КСК'),
    //'notify'=>X3::translate('Оповещений'),
    'message'=>X3::translate('Сообщений'),
    //'themes'=>X3::translate('Тем обсуждения'),
);
if(X3::user()->isAdmin() || X3::user()->isKsk()){
    //$srch['report'] = X3::translate('Отчетов');
}
//$srch['questions'] = X3::translate('Опросов');
$form = new Form();
?>
<?if(X3::user()->isGuest()):?>
<h1><a href="/">eКСК</a></h1>
<? else: ?>
<div class="fright user-block">
    <a class="fright quit" href="/user/logout.html"><img src="/images/quit.png" title="Выход" alt="X" /></a>
    <a class="user" href="/user/"><?=X3::user()->fullname?></a>
</div>
<div class="fleft inline-block">
    <?=$form->start(array('action'=>'/search.html'))?>
    <div class="wrapper inline-block">
        <?=$form->input('', array('name'=>'q[word]','placeholder'=>X3::translate('Поиск'),'class'=>'no-rt no-rb'))?>
        <?=$form->select($srch, array('name'=>'q[type]','%select'=>(isset(X3::user()->search['type']))?X3::user()->search['type']:'user','fcselect'=>'1','data-width'=>'155','data-fcclass'=>'select no-lt no-lb no-rt no-rb'))?>
        <?=X3_Html::form_tag('button', array('type'=>'submit','%content'=>(isset(X3::user()->search['word']) && X3::user()->search['word']!='')?X3::user()->search['word']:X3::translate('Искать'),'class'=>'no-lt no-lb'))?>
    </div>
    <?=$form->end()?>
</div>
<div class="fleft separator">&nbsp;</div>
<? endif; ?>
