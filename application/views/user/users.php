<?php
$users = Message::getUserList();
if($type == 'ksk'){
    $title = X3::translate('КСК');
    $cntmsg = X3::translate('КСК');
    $adding = X3::translate('Добавление КСК');
}elseif($type == 'user'){
    $adding = X3::translate('Добавление жильца');
    $title = X3::translate('Жильцы');
    $cntmsg = X3::translate('Жильцов');
}
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <?if(X3::user()->isAdmin()):?>
            <div class="wrapper inline-block"><a class="button inline-block" id="add_user" href="#user/add.html"><?=X3::translate('Добавить');?></a></div>
            <?endif;?>
        </div>
        <h1><?=$title?></h1>
    </div>
    <div class="content">
        <div class="stats">
            <?=$cntmsg?>: <?=$count?>
        </div>
        <table class="admin-list">
            <?foreach($models as $model):?>
            <tr>
                <td class="ava"><img src="<?=$model->avatar?>" width="100" alt="" /></td>
                <td class="name"><a href="/user/<?=$model->id?>.html"><?=$model->fullname?><?=$model->status==0?'<em>'.X3::translate('Не активирован').'</em>':''?></a></td>
                <td class="ops">
                    <a class="send_message" href="/message/with/<?=$model->id?>.html"><span><?=X3::translate('Написать сообщение')?></span></a>
                    <?if(X3::user()->isAdmin() || X3::user()->isKsk()):?>
                        <?if($model->status == 2):?>
                            <a href="/user/unblock/id/<?=$model->id?>.html"><span><?=X3::translate('Разблокировать')?></span></a>
                        <?else:?>
                            <a href="/user/block/id/<?=$model->id?>.html"><span><?=X3::translate('Блокировать')?></span></a>
                        <?endif;?>
                    <?endif;?>
                    <?if(X3::user()->isAdmin()):?>
                    <a onclick="return confirm('<?=X3::translate('Вы действительно хотите удалить эту запись?')?>');" href="/user/delete/id/<?=$model->id?>.html"><span><?=X3::translate('Удалить')?></span></a>
                    <?endif;?>
                </td>
            </tr>
            <?endforeach;?>
        </table>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/html" id="form_tmpl">
    <form method="post" action="/user/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form">
            <tr>
                <td class="label">
                    <label for="email">E-mail</label>
                </td>
                <td class="field">
                    <input type="hidden" name="type" value="<?=$type?>"  />
                    <input type="text" name="email"  />
                </td>
            </tr>
        </table>
    </form>
</script>
<script type="text/html" id="msg_tmpl">
    <form method="post" action="/message/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form" width="100%">
            <tr>
                <td class="label" width="70">
                    <label for="Message[user_to]"><?=Message::getInstance()->fieldName('user_to')?></label>
                </td>
                <td class="field" style="padding:5px 0px">
                    <input type="hidden" name="Message[user_to]"  value="" />
                    <span id="user_to"></span>
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
    var users = <?=json_encode($users)?>;
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
            var ut = eform.find('#user_to');
            ut.html(users[uid]);
            ut.siblings('input').val(uid);
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
        
////////////////////////////////////////////////////////
/////////////ADD ADMIN LOGIC////////////////////////////
////////////////////////////////////////////////////////
        $('#add_user').click(function(){
            var eform = $($('#form_tmpl').html());
            $.dialog(eform,'<?=$adding;?>',{caption:'<?=X3::translate('Добавить');?>',callback:function(){
                $.loader();
                var self = this;
                var action = eform.attr('action');
                $.post(action,eform.serialize(),function(m){
                    $.loader();
                    eform.find('.errors').css('display','none').html('');
                    if(m.status == 'error'){                        
                        eform.find('.errors').css('display','block').html(m.message);
                    }else{
                        self.close()
                        $.dialog(m.message,'<?=$adding;?>',{callback:function(){this.close()},caption:'Закрыть'});
                    }
                },'json').error(function(){
                    $.loader();
                    eform.find('.errors').css('display','block').html('<?=X3::translate('Ошибка в системе. Попробуйте позднее.');?>')
                })
                return false;
            }});
            return false;
        })
    })
</script>