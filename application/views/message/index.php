<?php
if(!is_string($paginator)){
    //TODO: users to whom are allowed to send message.
    $uq = X3::db()->query("SELECT id, CONCAT(name,' ',surname) AS username FROM data_user WHERE status>0 AND id<>".X3::user()->id);
    $users = array();
    while($u = mysql_fetch_assoc($uq))
        $users[$u['id']] = $u['username'];
}
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" id="send_message" href="#message/send.html"><?=X3::translate('Написать сообщение')?></a></div>
        </div>
        <h1><?=X3::translate('Мои сообщения');?></h1>
    </div>
    <div class="content">
        <div class="admin-list">
            <?foreach($models as $user):
                $model = Message::get(array('@condition'=>array(array('user_from'=>$user->id,'user_to'=>X3::user()->id),array('user_to'=>$user->id,'user_from'=>X3::user()->id)),'@order'=>'created_at DESC','@limit'=>'1'),1);
                ?>
                <div href="/message/with/<?=$user->id?>.html" class="message_block<?=$model->status==1 || $model->user_from==X3::user()->id?'':' unread'?>">
                    <div class="inside_block">
                        <div class="left_side">
                            <a href="/message/with/<?=$user->id?>.html">
                                <img width="100" src="/images/default.png" />
                            </a>
                        </div>
                        <div class="middle_side">
                                <a href="/user/<?=$user->id?>.html"><?=$user->name?> <?=$user->surname?></a>
                                <i><?=I18n::date($model->created_at)?>, <?=date("H:i",$model->created_at)?></i>
                        </div>
                        <div class="right_side">
                            <p><?=X3_String::create($model->content)->carefullCut(512);?></p>
                            <div class="wrapper"><a href="/message/with/<?=$user->id?>.html" class="button"><?=X3::translate('Ответить')?></a></div>
                        </div>
                    </div>
                </div>
            <?endforeach;?>
        </div>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/html" id="form_tmpl">
    <form method="post" action="/message/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form" width="100%">
            <tr>
                <td class="label" width="70">
                    <label for="Message[user_to]"><?=Message::getInstance()->fieldName('user_to')?></label>
                </td>
                <td class="field" style="padding:5px 0px">
                    <?if(is_string($paginator)):?>
                    <input type="hidden" name="Message[user_to]"  value="<?=$user->id?>" />
                    <span id="user_to"><?=$user->name?> <?=$user->surname?></span>
                    <?else:?>
                    <select fcselect name="Message[user_to]">
                    <?foreach($users as $id=>$name):?>
                        <option value="<?=$id?>"><?=$name?></option>
                    <?endforeach;?>
                    </select>
                    <?endif;?>
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
    var file_tpl = '<span class="file_link"><a filetitle href="#">Скриншот ошибки</a><a fileremove class="red_cross" href="#"><img width="7" height="" src="/images/zeropic.png" /></a></span>';
    $(function(){
        $('#send_message').click(function(){
            var eform = $($('#form_tmpl').html());
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
                        self.close()
                        $.dialog(m.message,'<?=X3::translate('Новое сообщение');?>',{callback:function(){this.close()},caption:'Закрыть'});
                    }
                },'json').error(function(){
                    $.loader();
                    eform.find('.errors').css('display','block').html('<?=X3::translate('Ошибка в системе. Попробуйте позднее.');?>')
                })
                return false;
            });
            eform.find('[fcselect]').fcselect({width:602});
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
        $('.message_block').css('cursor','pointer').click(function(){
            location.href = $(this).attr('href');
        });
    })
</script>