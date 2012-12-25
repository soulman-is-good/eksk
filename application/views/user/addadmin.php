<?php
$errors = $user->getTable()->getErrors();
$form = new Form($user);
?>
<div class="eksk-wnd<?=(!X3::user()->isGuest()?'':' login')?>">
    <div class="head"><h1<?=(!X3::user()->isGuest()?'':' class="center"')?>><?=X3::translate('Добавление администратора');?></h1></div>
    <div class="content">
        <?if(!empty($errors)):?>
        <div class="errors">
            <ul>
                <?foreach($errors as $errs):?>
                    <?foreach($errs as $err):?>
                <li><?=$err?></li>
                    <?endforeach;?>
                <?endforeach;?>
            </ul>
        </div>
        <?endif;?>
        <?=$form->start()?>
        <table class="eksk-form">
        <?
        echo $form->renderPartial(array('name','surname','email','password'));
        ?>
            <tr><td>&nbsp;</td><td align="left" colspan="2"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Добавить');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
