<?php
$user = new User;
$form = new Form($user);
?>
<div class="eksk-wnd login" id="login">
    <div class="head"><a href="#" onclick="$('.body').addClass('flipped')"><?=X3::translate('Зарегистрироваться');?></a><h1 class="center"><?=X3::translate('Авторизация');?></h1></div>
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
        //echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail'),'password'=>X3::translate('Пароль')));
        echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail или мобильный телефон'),'password'=>X3::translate('Пароль')));
        ?>
            <tr><td align="center" colspan="3"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Войти');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<div class="eksk-wnd login" id="recover">
    <div class="head"><a href="#" onclick="$('.body').removeClass('flipped')"><?=X3::translate('Авторизироваться');?></a><h1 class="center"><?=X3::translate('Регистрация жильца');?></h1></div>
    <div class="content">
        <?if($error!=''):?>
        <div class="errors">
            <ul>
                <li><?$error?></li>
            </ul>
        </div>
        <?endif;?>
        <em>В разработке</em>
        <?/*$form->start()?>
        <table class="eksk-form login-form">
        <?
        //echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail'),'password'=>X3::translate('Пароль')));
        echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail или мобильный телефон'),'password'=>X3::translate('Пароль')));
        ?>
            <tr><td align="center" colspan="3"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Войти');?></button></div></td></tr>
        </table>
        <?=$form->end()*/?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $(function(){
        $('#User_email').attr({'placeholder':'например +7 777 123 45 67 или 777 123 45 67'})
    })
</script>