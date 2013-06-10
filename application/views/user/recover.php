<?php
$form = new Form($user);
?>
<div class="eksk-wnd<?=(!X3::user()->isGuest()?'':' login')?>">
    <div class="head"><h1<?=(!X3::user()->isGuest()?'':' class="center"')?>>Восстановление пароля</h1></div>
    <div class="content">
        <?if($err!=false):?>
        <div class="errors">
            <ul>
                <li><?=$err?></li>
            </ul>
        </div>
        <?endif;?>
        <?=$form->start()?>
        <table class="eksk-form">
            <tr>
                <td class="label">
                    <label>Введите Ваш E-Mail</label>
                </td>
                <td class="field">
                    <input type="text" maxlength="255" value="" name="User[email]" id="User_email" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr><td>&nbsp;</td><td align="left" colspan="2"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Восстановить');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>