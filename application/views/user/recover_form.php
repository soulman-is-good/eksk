<?php
$form = new Form();
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
                    <label>Введите пароль</label>
                </td>
                <td class="field">
                    <input type="password" maxlength="255" value="" name="passwd" id="passwd" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="label">
                    <label>Повторите пароль</label>
                </td>
                <td class="field">
                    <input type="password" maxlength="255" value="" name="passwd_rpt" id="passwd_rpt" />
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr><td>&nbsp;</td><td align="left" colspan="2"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Изменить пароль');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>