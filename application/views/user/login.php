<?php
$form = new Form($user);
$form1 = new Form($address);
$errors = $user->getTable()->getErrors();
$errors = array_merge($errors,$address->getTable()->getErrors());
?>
<div class="eksk-wnd login noresize" id="login">
    <div class="head"><a href="#" onclick="$('.body').addClass('flipped');return false;"><?=X3::translate('Зарегистрироваться');?></a><h1 class="center"><?=X3::translate('Авторизация');?></h1></div>
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
<div class="eksk-wnd login noresize" id="recover">
    <div class="head"><a href="#" onclick="$('.body').removeClass('flipped')"><?=X3::translate('Авторизироваться');?></a><h1 class="center"><?=X3::translate('Регистрация жильца');?></h1></div>
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
        <table class="eksk-form login-form">
        <?
        //echo $form->renderPartial(array('email'=>X3::translate('Ваш E-mail'),'password'=>X3::translate('Пароль')));
        echo $form->renderPartial(array(
            'email'=>X3::translate('Ваш E-mail'),
            'password'=>X3::translate('Пароль'),
            'password_repeat'=>X3::translate('Повторите пароль')));
        ?>
            <tr>
                <td class="label">
                    <label><?=X3::translate('Город проживания')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=$form1->select('city_id',array('class'=>'city_id','fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?//$form1->error('city_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$address->fieldName('region_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('class'=>'region_id','name'=>'User_Address[region_id]','id'=>'User_Address_region_id','rid'=>$address->region_id,'fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?//$form->error('region_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$address->fieldName('house')?></label>
                </td>
                <td class="field">
                        <div class="wrapper inline-block"><select fcselect data-width="47" name="User_Address[house]" id="User_Address_house" hid="<?=addslashes($address->house)?>"></select></div>
                        <label><?=$address->fieldName('flat')?></label>
                        <div class="wrapper inline-block"><?=$form1->input('flat',array('style'=>"width:47px"))?></div>
                </td>
                <td class="error">
                    <?//$form1->error('house')?>
                </td>
            </tr>
            <tr>
                <td class="label">&nbsp;</td>
                <td class="field" colspan="2"><em><?=X3::translate('После регистрации Вы сможете добавить еще адреса');?></em></td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=X3::translate('Введите код на картинке');?></label>
                </td>
                <td class="field" colspan="2">
                    <div class="wrapper inline-block"><input name="captcha" id="captcha" value="" style="width:167px" type="text" /></div>
                    <a href="#update" onclick="$(this).children('img').attr('src','/uploads/captcha.gif?F5='+Math.random()*100);return false;"><img width="178" height="28" src="/uploads/captcha.gif" /></a>
                </td>
            </tr>
            <tr>
                <td class="label">
                </td>
                <td class="field">
                    <?=$form->checkbox('iagree')?>&nbsp;<label for="User_iagree"><?=$user->fieldName('iagree')?></label>
                </td>
                <td class="error">
                    <?//$form->error('region_id')?>
                </td>
            </tr>
            <tr><td align="center" colspan="3"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Зарегистрироваться');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $(function(){
        $('#User_email').attr({'placeholder':'например +7 777 123 45 67 или 777 123 45 67'})
        
        
        //Address
        $('.city_id').live('change',function(){
            var id = $(this).attr('id').split('_').pop();
            var city_id = $(this).val();
            var C = this;
            $.get('/city/region.html',{id:city_id},function(m){
                var R = $('#User_Address_region_id');
                var rid = R.attr('rid');
                R.html('');
                //R.data('fcselect').destroy();
                for(i in m){
                    var o = $('<option />').attr({'value':m[i].id}).data('houses',m[i].houses).html(m[i].title);
                    if(m[i].id == rid)
                        o.attr('selected',true);
                    $('#User_Address_region_id').append(o);
                }
                R.data('fcselect').redraw()
                $(C).parent().parent().parent().parent().find('.region_id').change();
            },'json')
        })
        $('.region_id').live('change',function(){
            var id = $(this).attr('id').split('_').pop();
            var H = $('#User_Address_house');
            var house = H.attr('hid');
            H.html('');
            var m = $(this).children(':selected').data('houses');
            for(i in m){
                var o = $('<option />').attr({'value':m[i]}).html(m[i]);
                if(m[i] == house)
                    o.attr('selected',true);
                H.append(o);
            }
            H.data('fcselect').redraw();
        })
        $('.city_id').change();
        <?if(!empty($errors)):?>
        $('.body').addClass('flipped');
        <?endif;?>
    })
</script>