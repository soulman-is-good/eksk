<?php
$user = new User;
$form = new Form($user);
?>
<div class="eksk-wnd login">
    <div class="head"><h1 class="center"><?=X3::translate('Авторизация');?></h1></div>
    <div class="content">
        <?if($error!=''):?>
        <div class="errors">
            <ul>
                <li><?=$error?></li>
            </ul>
        </div>
        <?endif;?>
        <?=$form->start()?>
        <table class="eksk-form login-form">
        <?
        echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail'),'password'=>X3::translate('Пароль')));
        //echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail или мобильный телефон'),'password'=>X3::translate('Пароль')));
        ?>
            <tr><td align="center" colspan="3"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Войти');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
