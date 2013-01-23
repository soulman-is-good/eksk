<?php
$form = new Form($user);
$form2 = new Form($profile);
$addreses = User_Address::get(array('user_id'=>$user->id));
if(($acnt = $addreses->count())==0){
    //$acnt = 1;    
    //$addreses = array(new User_Address);
}
$uerr = $user->getTable()->getErrors();
$perr = $profile->getTable()->getErrors();
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <?/*<div class="wrapper inline-block"><a class="button inline-block" id="add_user" href="#user/add.html">Добавить жильца</a></div>*/?>
        </div>
        <h1><?=X3::translate('Редактирование профиля');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs>
            <ul>
                <li><a href="#main-info"><?=X3::translate('Основная информация')?></a></li>
                <li><a href="#contact-info"><?=X3::translate('Контактная информация')?></a></li>
                <li><a href="#login-settings"><?=X3::translate('Настройки входа')?></a></li>
                <li><a href="#mail-settings"><?=X3::translate('Настройки уведомлений')?></a></li>
            </ul>
        <?=$form->start();?>
            <div class="tab" id="main-info">
                <table class="eksk-form">
                    <tr>
                        <td class="label">
                            <?if($user->role == 'ksk'):?>
                            <label><?=X3::translate('Название КСК')?></label>
                            <?else:?>
                            <label><?=$user->fieldName('name')?></label>
                            <?endif;?>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('name')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('name')?>
                        </td>
                    </tr>
                    <?if($user->role == 'ksk'):?>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('name')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('kskname')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('kskname')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('surname')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('ksksurname')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('ksksurname')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('duty')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('duty')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('duty')?>
                        </td>
                    </tr>
                    <?else:?>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('surname')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('surname')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('surname')?>
                        </td>
                    </tr>
                    <?endif;?>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('gender')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->select(array('Мужской'=>X3::translate('Мужской'),'Женский'=>X3::translate('Женский')),array('fcselect'=>'1','%select'=>$user->gender,'data-width'=>'345'))?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('gender')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('date_of_birth')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block">
                                <select fcselect name="User[date_of_birth][0]" data-width="28">
                                    <?for($i=1;$i<31;$i++):?>
                                    <option value="<?=$i?>" <?=(int)date('d',$user->date_of_birth)==$i?'selected="selected"':''?>><?=$i?></option>
                                    <?endfor;?>
                                </select>
                            </div>
                            <div class="wrapper inline-block">
                                <select fcselect name="User[date_of_birth][1]" data-width="187">
                                    <?for($i=1;$i<12;$i++):?>
                                    <option value="<?=$i?>" <?=(int)date('m',$user->date_of_birth)==$i?'selected="selected"':''?>><?=I18n::months($i-1,I18n::DATE_MONTH)?></option>
                                    <?endfor;?>
                                </select>
                            </div>
                            <div class="wrapper inline-block">
                                <select fcselect name="User[date_of_birth][2]" data-width="50">
                                    <?for($i=date('Y');$i>date('Y')-100;$i--):?>
                                    <option value="<?=$i?>" <?=(int)date('Y',$user->date_of_birth)==$i?'selected="selected"':''?>><?=$i?></option>
                                    <?endfor;?>
                                </select>
                            </div>
                        </td>
                        <td class="error">
                            <?=$form->error('date_of_birth')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('about')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapperEx inline-block"><?=$form2->textarea('about')?></div>
                        </td>
                        <td class="error">
                            <?=$form2->error('about')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('image')?></label>
                        </td>
                        <td class="field">
                            <?=$form->file('image')?>
                        </td>
                        <td class="error">
                            <?=$form->error('image')?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><button type="submit"><?=X3::translate('Сохранить')?></button></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div class="tab" id="contact-info">
                <?if(!empty($adrerrors)):?>
                <div class="errors">
                    <ul>
                        <?foreach($adrerrors as $adrer):?>
                        <li style="background: none"><?=$adrer?></li>
                        <?endforeach;?>
                    </ul>
                </div>
                <?endif;?>
                <table class="eksk-form">
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('mobile')?> +7</label>
                        </td>
                        <td class="field">
                            <?=$form2->input('mobile')?>
                        </td>
                        <td class="error">
                            <?=$form2->error('mobile')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('home')?> +7</label>
                        </td>
                        <td class="field">
                            <?=$form2->input('home')?>
                        </td>
                        <td class="error">
                            <?=$form2->error('home')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('work')?> +7</label>
                        </td>
                        <td class="field">
                            <?=$form2->input('work')?>
                        </td>
                        <td class="error">
                            <?=$form2->error('work')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('skype')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form2->input('skype')?></div>
                        </td>
                        <td class="error">
                            <?=$form2->error('skype')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('email')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form2->input('email')?></div>
                        </td>
                        <td class="error">
                            <?=$form2->error('email')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$profile->fieldName('site')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form2->input('site')?></div>
                        </td>
                        <td class="error">
                            <?=$form2->error('site')?>
                        </td>
                    </tr>
                    <?$i=0;foreach($addreses as $address):
                        $aform = new Form($address);
                        ?>
                    <?if($user->role == 'ksk' && $i==0):?>
                    <tr><td></td><td colspan="2"><h3><?=X3::translate('Адрес офиса');?></h3></td></tr>
                    <?endif;?>
                    <?if($user->role == 'ksk' && $i==1):?>
                    <tr><td></td><td colspan="2"><h3><?=X3::translate('Мои дома');?></h3></td></tr>
                    <?endif;?>
                    <tr class="address" link="L_<?=$i?>">
                        <td colspan="2">
                            <table class="eksk-form">
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('city_id')?></label>
                                    </td>
                                    <td class="field">
                                        <?if(!$address->getTable()->getIsNewRecord()):?>
                                        <?=$aform->hidden('id',array('name'=>"Address[$i][id]",'id'=>"Address_id_$i"))?>
                                        <?=($user->role == 'ksk' && $i==0)?$aform->hidden('status',array('name'=>"Address[$i][status]",'id'=>"Address_status_$i",'value'=>'0')):''?>
                                        <?=$i>0?X3_Html::form_tag('input', array('type'=>'checkbox','class'=>'delete_address','style'=>'display:none','name'=>"Address[$i][delete]")):''?>
                                        <?endif;?>
                                        <div class="wrapper inline-block"><?=$aform->select('city_id',array('name'=>"Address[$i][city_id]",'id'=>"Address_city_id_$i",'class'=>'city_id','fcselect'=>'1','data-width'=>'345'))?></div>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('city_id')?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('region_id')?></label>
                                    </td>
                                    <td class="field">
                                        <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('name'=>"Address[$i][region_id]",'id'=>"Address_region_id_$i",'class'=>'region_id','rid'=>$address->region_id,'fcselect'=>'1','data-width'=>'345'))?></div>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('region_id')?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('house')?></label>
                                    </td>
                                    <td class="field">
                                        <?if($user->role == 'user'):?>
                                            <div class="wrapper inline-block"><select fcselect data-width="47" name="Address[<?=$i?>][house]" id="Address_house_<?=$i?>" hid="<?=addslashes($address->house)?>"></select></div>
                                            <label><?=$address->fieldName('flat')?></label>
                                            <div class="wrapper inline-block"><?=$aform->input('flat',array('name'=>"Address[$i][flat]",'id'=>"Address_flat_$i",'style'=>"width:47px"))?></div>
                                            <?else:?>
                                            <?if($i==0):?>
                                            <div class="wrapper inline-block"><?=$aform->input('house',array('name'=>"Address[$i][house]",'id'=>"Address_house_$i",'style'=>"width:47px"))?></div>
                                            <label><?=$address->fieldName('flat')?></label>
                                            <div class="wrapper inline-block"><?=$aform->input('flat',array('name'=>"Address[$i][flat]",'id'=>"Address_flat_$i",'style'=>"width:47px"))?></div>
                                            <?else:?>
                                            <div class="wrapper inline-block"><?=$aform->input('house',array('name'=>"Address[$i][house]",'id'=>"Address_house_$i"))?></div>
                                            <?=$aform->hidden('flat',array('name'=>"Address[$i][flat]",'id'=>"Address_flat_$i",'value'=>"0"))?>
                                            <?endif;?>
                                        <?endif;?>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('house')?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align: middle;text-align: center">
                            <?if($i>0):?>
                            <a href="#remove" class="map_link remove"><span><?=X3::translate('Удалить');?></span></a>
                            <?endif;?>
                        </td>
                    </tr>
                    <tr class="map_tpl" link="L_<?=$i?>">
                        <td><input type="hidden" id="coord_<?=$i?>" name="Address[<?=$i?>][coord]" value="<?=$address->coord?>" /></td>
                        <td colspan="2">
                            <a class="map_link inline-block mb-10 map-link" href="#coord_<?=$i?>"><span><?=X3::translate('Указать на карте');?></span><span style="display:none"><?=X3::translate('Спрятать карту');?></span></a>
                            <div class="map" style="display:none">
                                <div></div>
                            </div>
                        </td>
                    </tr>
                    <?$i++;endforeach;$i++;$address = new User_Address;$aform = new Form($address);?>
                    <?if($user->role == 'ksk' && $i==2):?>
                    <tr><td></td><td colspan="2"><h3><?=X3::translate('Мои дома');?></h3></td></tr>
                    <?endif;?>                    
                    <tr class="address new" link="L_<?=$i?>">
                        <td colspan="2">
                            <table class="eksk-form">
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('city_id')?></label>
                                    </td>
                                    <td class="field">
                                        <div class="wrapper inline-block"><?=$aform->select('city_id',array('name'=>"Address[$i][city_id]",'id'=>"Address_city_id_$i",'class'=>'city_id','fcselect'=>'1','data-width'=>'345'))?></div>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('city_id')?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('region_id')?></label>
                                    </td>
                                    <td class="field">
                                        <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('name'=>"Address[$i][region_id]",'id'=>"Address_region_id_$i",'class'=>'region_id','rid'=>$address->region_id,'fcselect'=>'1','data-width'=>'345'))?></div>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('region_id')?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">
                                        <label><?=$address->fieldName('house')?></label>
                                    </td>
                                    <td class="field">
                                            <?if($user->role == 'user'):?>
                                            <div class="wrapper inline-block"><select fcselect data-width="47" name="Address[<?=$i?>][house]" id="Address_house_<?=$i?>" hid="<?=addslashes($address->house)?>"></select></div>
                                            <label><?=$address->fieldName('flat')?></label>
                                            <div class="wrapper inline-block"><?=$aform->input('flat',array('name'=>"Address[$i][flat]",'id'=>"Address_flat_$i",'style'=>"width:47px"))?></div>
                                            <?else:?>
                                            <div class="wrapper inline-block"><?=$aform->input('house',array('name'=>"Address[$i][house]",'id'=>"Address_house_$i"))?></div>
                                            <?=$aform->hidden('flat',array('name'=>"Address[$i][flat]",'id'=>"Address_flat_$i",'value'=>"0"))?>
                                            <?endif;?>
                                    </td>
                                    <td class="error">
                                        <?=$aform->error('house')?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="additional" width="185">
                            <a href="#add" class="map_link add_address"><span><?=X3::translate('Добавить еще адрес');?></span></a>
                        </td>
                    </tr>
                    <tr class="map_tpl" link="L_<?=$i?>">
                        <td><input type="hidden" id="coord_<?=$i?>" name="Address[<?=$i?>][coord]" value="<?=$address->coord?>" /></td>
                        <td colspan="2">
                            <a class="map_link inline-block mb-10 map-link" href="#coord_<?=$i?>"><span><?=X3::translate('Указать на карте');?></span><span style="display:none"><?=X3::translate('Спрятать карту');?></span></a>
                            <div class="map" style="display:none">
                                <div></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><button type="submit"><?=X3::translate('Сохранить')?></button></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
        <?=$form->end();$acnt++;?>
            <div class="tab" id="login-settings">
            <form method="post">
                <table class="eksk-form">
                    <tr>
                        <td class="label">
                            &nbsp;
                        </td>
                        <td class="field">
                            <h3><?=X3::translate('Изменить пароль')?></h3>
                        </td>
                        <td class="error">
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('password_old')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=X3_Html::form_tag('input',array('type'=>'password','name'=>'Change[password_old]'))?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('password_old')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('password_new')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=X3_Html::form_tag('input',array('type'=>'password','name'=>'Change[password_new]'))?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('password_new')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('password_repeat')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=X3_Html::form_tag('input',array('type'=>'password','name'=>'Change[password_repeat]'))?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('password_repeat')?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><button type="submit"><?=X3::translate('Сменить пароль')?></button></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
                <div class="hr">&nbsp;</div>
            <form method="post">
                <table class="eksk-form">
                    <tr>
                        <td class="label">
                            &nbsp;
                        </td>
                        <td class="field">
                            <h3><?=X3::translate('E-mail и телефон для входа')?></h3>
                        </td>
                        <td class="error">
                            &nbsp;
                        </td>
                    </tr>                    
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('email')?></label>
                        </td>
                        <td class="field">
                            <div class="wrapper inline-block"><?=$form->input('email')?></div>
                        </td>
                        <td class="error">
                            <?=$form->error('email')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            <label><?=$user->fieldName('phone')?></label>
                        </td>
                        <td class="field">
                            <?=$form->input('phone')?>
                        </td>
                        <td class="error">
                            <?=$form->error('phone')?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><button type="submit"><?=X3::translate('Сохранить')?></button></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
            </div>
            <div class="tab" id="mail-settings">
                <em>В разработке</em>
                <?/*
                <?=SysSettings::getValue('User_Settings.Notify', 'text', 'Текст в настройках уведомлений', null, '<p>Внимание! Укажите номер телефона чтобы получить уведомления о событиях на сайте.</p>
                <p>Отметьте события о которых вы хотите получать уведомления по эл. почте или в SMS (для абонентов Билайн, KCell, Active, TELE2, Pathword, Дос в Казахстане)</p>')?>
                <br/>
                <form method="post">
                    <table class="eksk-form" width="50%">
                        <tr>
                            <td class="label">
                                &nbsp;
                            </td>
                            <td class="field" style="text-align:center">
                                <h3><?=X3::translate('Эл. почта')?></h3>
                            </td>
                            <td class="field" style="text-align:center">
                                <h3>SMS</h3>
                            </td>
                        </tr>                    
                        <tr>
                            <td class="label">
                                <label><?=X3::translate('Мои оповещения');?></label>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">
                                <label><?=X3::translate('Мои сообщения');?></label>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">
                                <label><?=X3::translate('Темы обсуждения');?></label>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">
                                <label><?=X3::translate('Опросы (голосования)');?></label>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                            <td class="field" style="text-align:center">
                                <?=X3_Html::form_tag('input', array('type'=>'checkbox'))?>
                            </td>
                        </tr>
                    </table>
                <div class="hr">&nbsp;</div>
                    <table class="eksk-form" >
                        <tr>
                            <td class="label">
                                <label><?=X3::translate('Присылать SMS с');?></label>
                            </td>
                            <td class="field">
                                <div class="wrapper inline-block"><?=X3_Html::form_tag('input', array('type'=>'text','style'=>'width:48px','class'=>'string time'))?></div>
                            </td>
                            <td class="field">
                                <label><?=X3::translate('до');?></label> 
                                <div class="wrapper inline-block"><?=X3_Html::form_tag('input', array('type'=>'text','style'=>'width:48px','class'=>'string time'))?></div> 
                                <label><?=X3::translate('по алматинскому времени');?></label>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><button type="submit"><?=X3::translate('Сохранить')?></button></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>*/?>
            </div>
        </div>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/javascript">
    var addrtmpl = '';
    var maptmpl = '';
    var success = '<div class="success"><?=X3::translate('Сохранено');?></div>';
    var acnt = <?=++$acnt;?>;
    addrtmpl = $('.address:last').html();
    maptmpl = $('.map_tpl:last').html();
    $(function(){
        $.mask.definitions['w'] = '[0-9]{0,1}';
        //$.mask.definitions['p'] = '[0-9]{2,5} [0-9]{2,5}';
        function phone(el,o,t,cmsk,msk){
            var val = el.val().split(' ');
            var code = val.shift();
            var phone = val.join(' ');
            if(code.length>0 && o==5 && code.length < 5)
                code = code + "_".repeat(5-code.length);
            if(phone.length>0 && t==9 && phone.length < 9){
                phone = phone + "_".repeat(9-phone.length);
            }
            if(phone.length>0 && t==18){
                phone = phone.split(' ');
                if(phone.length == 4){
                    if(phone[2].length == 1)
                        phone[2] += '_';
                    if(phone[3].length < 6)
                        phone[3] = phone[3] + "_".repeat(6-phone[3].length);
                    phone[3] = '(' + phone[3] + ')';
                }
                phone = phone.join(' ');
            }
            var in_code = $('<input />').data('elem',el).css({'width':'46px','padding-left':'0px','text-align':'right'}).change(function(){
                $(this).data('elem').updateVal();
            }).attr({'type':'text','maxlength':o}).addClass('string').mask(cmsk).val(code)
            .insertBefore(el).wrap($('<div class="wrapper inline-block" style="margin-right:5px"></div>'));
            var in_phone = $('<input />').data('elem',el).css({'width':'288px'}).change(function(){
                $(this).data('elem').updateVal();
            }).attr({'type':'text','maxlength':t}).addClass('string').mask(msk).val(phone)
            .insertBefore(el).wrap($('<div class="wrapper inline-block"></div>'));
            el.data({'code':in_code,'phone':in_phone}).updateVal = function(){
                var a = in_code.val();
                var b = in_phone.val().replace(/[)(]/g, '');
                if(a=='' && b=='')
                    $(this).val('');
                else
                    $(this).val((a+' '+b).replace(/\_/g, ''));
            }
            el.css({opacity:0,width:0,height:0,position:'absolute','left':'-9999px'}).attr({tabindex:'-1'});
        }
        phone($('#User_Settings_mobile'),3,9,"999","999 99 99");
        phone($('#User_Settings_home'),5,9,"999ww","999 99 9w");
        phone($('#User_Settings_work'),5,18,"999ww","999 99 9w (wwwwww)");
        phone($('#User_phone'),3,9,"999","999 99 99");
        //Address
        $('.city_id').live('change',function(){
            var id = $(this).attr('id').split('_').pop();
            var city_id = $(this).val();
            var C = this;
            $.get('/city/region.html',{id:city_id},function(m){
                var R = $('#Address_region_id_'+id);
                var rid = R.attr('rid');
                R.html('');
                //R.data('fcselect').destroy();
                for(i in m){
                    var o = $('<option />').attr({'value':m[i].id}).data('houses',m[i].houses).html(m[i].title);
                    if(m[i].id == rid)
                        o.attr('selected',true);
                    $('#Address_region_id_'+id).append(o);
                }
                R.data('fcselect').redraw()
                $(C).parent().parent().parent().parent().find('.region_id').change();
            },'json')
        })
        <?if($user->role == 'user'):?>
        $('.region_id').live('change',function(){
            var id = $(this).attr('id').split('_').pop();
            var H = $('#Address_house_'+id);
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
        <?endif;?>
        $('.city_id').change();
        
        <?if($hash):?>
            $('[href="<?=$hash?>"]').click();
        <?endif;?>
            
        //Init maps
        $('.map-link').live('click',function(){
            var href = $(this).attr('href');
            var coords = $(href).val().split('|');
            this.type = 'yandex#map';
            this.zoom = 16;
            var self = this;
            var nocoords = false;
            if(coords.length < 3 && typeof ymaps != 'undefined'){
                coords = [ymaps.geolocation.longitude,ymaps.geolocation.latitude];
                nocoords = true;
            }else if(coords.length < 3) {
                coords = [76.943776,43.295904];
                nocoords = true;
            }else{
                if(coords.length == 4)
                    this.zoom = coords.pop();
                this.type = coords.pop();
            }
            $(this).siblings('.map').slideToggle(function(){
                if(typeof $(this).data('map') == 'undefined'){
                    var id = href.split('_').pop();
                    var map = new ymaps.Map(this,{center:coords,zoom:self.zoom,type:self.type});
                    var zc = new ymaps.control.ZoomControl();
                    var ts = new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]);
                    map.controls.add(zc).add(ts);
                    zc.events.add('zoomchange',function(e){
                        self.zoom = e.get('newZoom');
                        setCoords(coords,id);
                    })
                    map.events.add('typechange',function(e){
                        self.type=e.get('newType');
                        setCoords(coords,id);
                    })
                    //var placemark = new ymaps.Placemark(coords,{iconContent: '<?=addslashes($user->name)?> <?=addslashes($user->surname)?>'},{preset: 'twirl#greenStretchyIcon'});
                    var placemark = new ymaps.Placemark(coords,{},{preset: 'twirl#greenStretchyIcon',draggable:true});
                    //On placemark dragging
                    placemark.events.add('dragend', function(e) {
                        coords = e.get('target').geometry.getCoordinates();
                        setCoords(coords,id);
                    });
                    map.geoObjects.add(placemark);
                    $('#Address_city_id_'+id+', #Address_region_id_'+id+', #Address_house_'+id).change(function(){
                        var id = $(this).attr('id').split('_').pop();
                        var text = '';
                        text += $('#Address_city_id_'+id).children(':selected').text();
                        <?if($user->role == 'user'):?>
                        text += ', ' + $('#Address_house_'+id).children(':selected').text();
                        <?else:?>
                        text += ', ' + $('#Address_house_'+id).val();
                        <?endif;?>
                        text += ', ' + $('#Address_region_id_'+id).children(':selected').text();
                        ymaps.geocode(text).then(function(res){
                            coords = res.geoObjects.get(0).geometry.getCoordinates();
                            setCoords(coords,id);
                            map.panTo(coords,{duration:2000});
                            placemark.geometry.setCoordinates(coords);
                        },function(err){})
                    })
                    if(nocoords)
                        $('#Address_house_'+id).change();
                    $(this).data({'map':map,'placemark':placemark});
                }else{
                    var map = $(this).data('map');
                    
                }
            });
            $(this).children('span').toggle();
            return false;
        })
        
        //add\remove addreses
        $('.add_address').live('click',function(){
            var a = $(maptmpl.replace(/Address\[[0-9]+\]/g,"Address["+acnt+"]").replace(/_[0-9]+"/g,'_'+acnt+'"').replace(/coord_[0-9]+/g,"coord_"+acnt));
            var b = $(addrtmpl.replace(/Address\[[0-9]+\]/g,"Address["+acnt+"]").replace(/_[0-9]+"/g,'_'+acnt+'"'));
            b.find('[fcselect]').each(function(){
                $(this).fcselect();
            });
            b.find('[id^="Address_id_"], .delete_address').remove();
            <?if($user->role == 'user'):?>
            b.find('[id^="Address_flat_"]').val('');
            <?else:?>
            b.find('[id^="Address_house_"]').val('');
            <?endif;?>
            //a.find('.remove').removeClass('remove').addClass('add_address').children('span').html('<?=X3::translate('Добавить еще адрес');?>')
            $('.address:last').find('.add_address').removeClass('add_address').addClass('remove').children('span').html('<?=X3::translate('Удалить');?>')
            $('<tr class="address new" link="L_'+acnt+'"></tr>').append(b).insertAfter($('.map_tpl:last'))
            $('<tr class="map_tpl new" link="L_'+acnt+'"></tr>').append(a).insertAfter($('.address:last'))
            b.find('.city_id').change();
            acnt++;
            return false;
        })
        $('.address .remove').live('click',function(){
            var m = $(this).parents('.address'); 
            var link = m.attr('link');
            if(m.hasClass('new')){
                    $('[link="'+link+'"]').remove();
            }else{
                m.find('.delete_address').attr('checked',true);
                m.toggle();
                $('[link="'+link+'"]').eq(1).remove();
            }
            return false;
        })
        <?//IF THER IS NO ERRORS
        if(empty($uerr) && empty($perr) && empty($adrerrors) && (isset($_POST['User']) || isset($_POST['Address']) || isset($_POST['User_Settings']))):?>
            var h = location.hash;
            if(h=='') h = '#main-info';
            $(h).prepend($(success).css('cursor','pointer').click(function(){$(this).fadeOut(function(){$(this).remove()})}))
        <?endif;?>
    })
    function setCoords(coords, id){
        if(typeof coords.join != 'function')
            return false;
        var val = coords.join('|');
        var a = $('[href="#coord_'+id+'"]');
        val += '|' + a[0].type;
        val += '|' + a[0].zoom
        $('#coord_'+id).val(val)
        return true;
    }
</script>