<?php
if(NULL === ($profile = User_Settings::get(array('user_id'=>$user->id),1)))
    $profile = new User_Settings;
$addreses = User_Address::get(array('user_id'=>$user->id));
?>
<div class="eksk-wnd">
    <div class="head">
        <em>
            <?if($user->isOnline()):?>
            <?=X3::translate('Онлайн');?>
            <?else:?>
            <?=X3::translate('Оффлайн');?>
            <?endif;?>
        </em>
        <h1><?=$user->fullname?></h1>
    </div>
    <div class="content">
        <div class="avaplace">
            <img src="<?=$user->getAvatar('233x233xw')?>" alt="" />
            <?if($user->id == X3::user()->id):?>
            <a href="/user/edit.html"><?=X3::translate('Редактировать профиль')?></a>
            <?else:?>
            <a class="send_message" href="/message/with/<?=$user->id?>.html"><?=X3::translate('Написать сообщение')?></a>
            <?endif;?>
            <?if($user->role == 'ksk'):?>
            <div class="with_stars">
                    <i><?=X3::translate('Рейтинг');?>:</i>
                    <?/*<img src="/images/zeropic.png" class="full" width="12" height="11">
                    <img src="/images/zeropic.png" class="half" width="12" height="11">*/?>
                    <img src="/images/zeropic.png" class="hollow" width="12" height="11">
                    <img src="/images/zeropic.png" class="hollow" width="12" height="11">
                    <img src="/images/zeropic.png" class="hollow" width="12" height="11">
                    <img src="/images/zeropic.png" class="hollow" width="12" height="11">
                    <img src="/images/zeropic.png" class="hollow" width="12" height="11">
            </div>
            <?endif;?>
        </div>
        <div class="main_info <?=$user->role?>-profile">
            <table>
                <tbody>
                <?if($user->role == 'ksk'):?>
                <tr>
                        <td class="one first" colspan="2">
                            <b><?=$user->kskname?> <?=$user->ksksurname?></b> <em><?=$user->duty?></em>
                        </td>
                </tr>
                <?endif;?>
                <?if(trim($profile->about)!=''):?>
                <tr>
                        <td class="one">
                                <em><?=X3::translate('О себе')?>:</em>
                        </td>
                        <td>
                                <span><?=nl2br($profile->about)?></span>
                        </td>
                </tr>
                <?endif;?>
                <tr>
                        <td class="one">
                                <em><?=X3::translate('Пол');?>:	</em>
                        </td>
                        <td>
                                <span><?=X3::translate($user->gender)?></span>
                        </td>
                </tr>
                <tr>
                        <td class="without" colspan="2">
                                <h4><?=X3::translate('Контактная информация')?>:</h4>
                        </td>
                </tr>
                <?if($profile->mobile != '' || $profile->home != '' || $profile->work != ''):?>
                <tr>
                        <td class="one">
                                <em><?=X3::translate('Телефон')?>:</em>
                        </td>
                        <td>
                                <span>+7 <?=$profile->mobile!=''?$profile->mobile:($profile->home!=''?$profile->home:($profile->work?$profile->work:''))?></span>
                        </td>
                </tr>
                <?endif;?>
                <?if($profile->skype != ''):?>
                <tr>
                        <td class="one">
                                <em>Skype:</em>
                        </td>
                        <td>
                                <span><?=$profile->skype?></span>
                        </td>
                </tr>
                <?endif;?>
                <?if($profile->site != ''):?>
                <tr>
                        <td class="one">
                            <em><?=X3::translate('Веб-сайт')?>:</em>
                        </td>
                        <td>
                            <a href="/site/go/url/<?=base64_encode($profile->site)?>" target="_blank"><?=$profile->site?></a>
                        </td>
                </tr>
                <?endif;?>
                <?$i=1;foreach($addreses as $address):$coord = explode('|',$address->coord);?>
                <tr class="no-border">
                        <td class="one">
                            <?if($user->role == 'ksk' && $address->status==0):?>
                                <em><?=X3::translate('Адрес офиса')?>:</em>
                            <?else:?>
                                <em><?=X3::translate('Адрес')?> <?=$i++?>:</em>
                            <?endif;?>
                        </td>
                        <td id="address<?=$address->id?>">
                                <span><?=$address->city->title?>, <?=$address->street->title?>, <?=$address->house?>, <?=X3::translate('квартира')?> <?=$address->flat?></span>
                                <?if(count($coord)==4):?>
                                <a data-aid="<?=$address->id?>" data-coord="<?=$address->coord?>" class="map_link map-link" href="#"><span><?=X3::translate('Показать на карте')?></span><i>&nbsp;</i></a>
                                <?endif;?>

                        </td>
                </tr>
                <?if(count($coord)==4):?>
                <tr>
                        <td class="map" colspan="2">
                                <div id="map<?=$address->id?>" class="map_image" style="display:none;">
                                        <div class="map-place"></div>
                                        <a class="zoom" data-aid="<?=$address->id?>" data-coord="<?=$address->coord?>" href="#"><?=X3::translate('Увеличить карту')?></a>
                                </div>
                        </td>
                </tr>
                <?endif;?>
                <?endforeach;?>
        </tbody></table>            
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/html" id="msg_tmpl">
    <form method="post" action="/message/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form" width="100%">
            <tr>
                <td class="label" width="70">
                    <label for="Message[user_to]"><?=Message::getInstance()->fieldName('user_to')?></label>
                </td>
                <td class="field" style="padding:5px 0px">
                    <input type="hidden" name="Message[user_to]"  value="<?=$user->id?>" />
                    <span id="user_to"><?=$user->fullname?></span>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="Message[content]"><?=Message::getInstance()->fieldName('content')?></label>
                </td>
                <td class="field">
                    <textarea name="Message[content]" style="width:623px"></textarea>
                    <input type="hidden" value="" name="files" id="files" />
                </td>
            </tr>
        </table>
    </form>
        <table class="eksk-form" width="100%">
            <tr>
                <td class="label">&nbsp;</td>
                <td class="field">
                    <div class="table_faq">
                        <div class="bg_form"><button id="send_btn"><?=X3::translate('Отправить')?></button></div>
                        <div class="att_files">
                                <table>
                                        <tbody><tr>
                                                <td><i><?=X3::translate('Прикрепленные файлы')?>:</i>
                                                </td>
                                                <td>
                                                        <div class="fix_links" id="file_list">
                                                        </div>
                                                </td>
                                        </tr>
                                </tbody></table>
                        </div>
                        <div class="faq_right">
                                <a href="#"><?=X3::translate('Прикрепить файл')?></a>
                                <form action="/message/file.html" method="post" enctype="multipart/form-data" target="for_files">
                                <input type="file" id="file" name="file" class="file" size="1" />
                                </form>
                                <iframe name="for_files" id="for_files"></iframe>
                        </div>
                        <div class="clear">&nbsp;</div>
                    </div>
                </td>
            </tr>
        </table>
</script>
<script>
    var file_tpl = '<span class="file_link"><a filetitle href="#"></a><a fileremove class="red_cross" href="#"><img width="7" height="" src="/images/zeropic.png" /></a></span>';
    $(function(){
        $('.send_message').click(function(){
            var uid = (/\/([0-9]+)\.html/).exec($(this).attr('href')).pop();
            var eform = $($('#msg_tmpl').html());
            var self = $.dialog(eform,'<?=X3::translate('Написать сообщение');?>','no buttons').setSize(750).setRelativePosition('center');
            eform.find('#send_btn').click(function(){
                $.loader();
                var action = eform.attr('action');
                $.post(action,eform.serialize(),function(m){
                    $.loader();
                    eform.find('.errors').css('display','none').html('');
                    if(m.status == 'error'){                        
                        eform.find('.errors').css('display','block').html(m.message);
                    }else{
                        self.close();
                        $.dialog(m.message,'<?=X3::translate('Новое сообщение');?>',{callback:function(){this.close()},caption:'Закрыть'});
                    }
                },'json').error(function(){
                    $.loader();
                    eform.find('.errors').css('display','block').html('<?=X3::translate('Ошибка в системе. Попробуйте позднее.');?>')
                })
                return false;
            });
            eform.find('#file').change(function(){
                $(this).parent().submit();
                $.loader();
            })
            eform.find('#for_files').load(function(){
                var html = $(this).contents().find('body').html();
                if(html!='' && html != null){
                    var json = eval('(' + html + ')');
                    if(typeof $.rusWindows['@loader'] !== 'undefined')
                        $.loader();
                    if(json.status == 'ok'){
                        var file = $(file_tpl);
                        file.find('[filetitle]').html(json.message.filename)
                            .attr({'target':'_blank','href':'/uploads/get/file/'+json.message.id});
                        file.find('[fileremove]').data('fid',json.message.id).click(function(){
                            var files = $('#files').val().split(',');
                            for(i in files)
                                if(files[i] == $(this).data('fid')){
                                    files.splice(i, 1);
                                    break;
                                }
                            $('#files').val(files.join(','));
                            $(this).parent().fadeOut(function(){$(this).remove()});
                        })
                        var files = $('#files').val().split(',');
                        files.push(json.message.id);
                        $('#files').val(files.join(','));
                        $('#file_list').append(file);
                    }else{
                        alert(json.message);
                    }
                }
            })
            return false;
        })
        $('.map-link').live('click',function(){
            $(this).toggleClass('active');
            var coords = $(this).data('coord').split('|');
            var id = $(this).data('aid');
            this.type = 'yandex#map';
            this.zoom = 16;
            var self = this;
            var nocoords = false;
            if(coords.length < 3 && typeof ymaps != 'undefined'){
                coords = [ymaps.geolocation.longitude,ymaps.geolocation.latitude];
                $(this).remove();
                nocoords = true;
                return false;
            }else if(coords.length < 3) {
                $(this).remove();
                nocoords = true;
                return false;
            }else{
                if(coords.length == 4)
                    this.zoom = coords.pop();
                this.type = coords.pop();
            }
            $('#map'+id).slideToggle(function(){
                var p = $(this).children('.map-place');
                if(typeof p.data('map') == 'undefined'){
                    var map = new ymaps.Map(p[0],{center:coords,zoom:self.zoom,type:self.type});
                    var zc = new ymaps.control.ZoomControl();
                    var ts = new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]);
                    map.controls.add(zc).add(ts);
                    var placemark = new ymaps.Placemark(coords,{},{preset: 'twirl#greenStretchyIcon',draggable:false});
                    map.geoObjects.add(placemark);
                    p.data({'map':map,'placemark':placemark});
                }
            });
            return false;
        })
        
        $('.zoom').click(function(){
            var h = $(window).height()-60;
            var w = $(window).width()-40;
            var id = $(this).data('aid');
            var coords = $(this).data('coord').split('|');
            var zoom = coords.pop();
            var type = coords.pop();
            var map = $('<div />').width(w).height(h-30);
            var wnd = map.css({height:(h)+'px',width:(w)+'px'}).rusWindow({
                title:$('#address'+id).children('span').html(),
                modal:true,
                height:h,
                width:w,
                position:'center'
            });
            var x = new ymaps.Map(map[0],{center:coords,zoom:zoom,type:type});
            var zc = new ymaps.control.ZoomControl();
            var ts = new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]);
            x.controls.add(zc).add(ts);
            var placemark = new ymaps.Placemark(coords,{},{preset: 'twirl#greenStretchyIcon',draggable:false});
            x.geoObjects.add(placemark);
            $(wnd.getContent()).css({'position':'fixed','top':'20px','left':'20px'})
            return false;
        })
    })
</script>