<?php
$srch = array(
    'user'=>X3::translate('Жильцов'),
    'ksk'=>X3::translate('КСК')
);
$form = new Form();
?>
<?if(X3::user()->isGuest()):?>
<h1>eКСК</h1>
<? else: ?>
<div class="fright user-block">
    <a class="fright quit" href="/user/logout.html"><img src="/images/quit.png" title="Выход" alt="X" /></a>
    <a class="user" href="/user/"><?=X3::user()->name?></a>
</div>
<div class="fleft inline-block">
    <?=$form->start()?>
    <div class="wrapper inline-block">
        <?=$form->input('', array('name'=>'q','placeholder'=>X3::translate('Поиск'),'class'=>'no-rt no-rb'))?>
        <?=$form->select($srch, array('name'=>'q','%select'=>'user','fcselect'=>'1','data-width'=>'155','data-fcclass'=>'select no-lt no-lb no-rt no-rb'))?>
        <?=X3_Html::form_tag('button', array('type'=>'submit','%content'=>X3::translate('Искать'),'class'=>'no-lt no-lb'))?>
    </div>
    <?=$form->end()?>
</div>
<div class="fleft separator">&nbsp;</div>
<? endif; ?>
