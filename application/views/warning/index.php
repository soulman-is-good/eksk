<?php
$id = X3::user()->id;
$me = (object)X3::db()->fetch("SELECT id, CONCAT(name,' ',surname) name, image, role FROM data_user WHERE id = ".$id);
$me->name = $me->role=='admin'?X3::translate('Администратор').'#'.$me->id:$me->name;
$me->avatar = '/images/default.png';
if(is_file('uploads/User/'.$me->image)){
        $me->avatar = '/uploads/User/50x50/' . $me->image;
}
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <?if(X3::user()->isKsk() || X3::user()->isAdmin()):?>
            <div class="wrapper inline-block"><a class="button inline-block" id="create_forum" href="/warning/create.html"><?=X3::translate('Создать')?></a></div>
            <?endif;?>
        </div>
        <h1><?=X3::translate('Оповещения');?></h1>
    </div>
    <div class="content">
        <table class="admin-list">
            <?while($model = mysql_fetch_object($models)):
                $user = (object)X3::db()->fetch("SELECT id, CONCAT(name,' ',surname) name, image, role FROM data_user WHERE id = ".$model->user_id);
                $user->name = $user->role=='admin'?X3::translate('Администратор').' #'.$user->id:$user->name;
                $model->avatar = '/images/default.png';
                if(is_file('uploads/User/'.$user->image))
                    $model->avatar = '/uploads/User/100x100/' . $user->image;
                ?>
                <tr>
                    <td class="ava">
                            <a href="/user/<?=$model->user_id?>.html">
                                <img width="100" src="<?=$model->avatar?>" />
                            </a>
                    </td>
                    <td class="name">
                            <a href="/user/<?=$model->user_id?>.html"><?=$user->name?></a><br/>
                            <em><?=I18n::date($model->latest)?>, <?=date("H:i",$model->latest)?></em>
                    </td>
                    <td class="text">
                            <p><?=nl2br($model->title);?></p>
                    </td>
                    <td class="ops">
                        <?if(X3::user()->id == $user->id):?>
                            <?if($model->status=='0'):?>
                                <a href="/warning/create/id/<?=$model->id?>.html"><span><?=X3::translate('Редактировать')?></span></a>
                                <a href="/warning/send/id/<?=$model->id?>.html"><span><?=X3::translate('Опубликовать')?></span></a>
                                <a href="/warning/delete/id/<?=$model->id?>.html"><span><?=X3::translate('Удалить')?></span></a>
                            <?else:?>
                                <em style="display:block;margin-bottom:15px"><?=X3::translate('Опубликовано')?></em>
                            <?endif;?>
                        <?elseif(!Warning::isKnown($model->id)):?>
                                <div class="wrapper"><a class="button" href="/warning/read/id/<?=$model->id?>.html"><?=X3::translate('Ознакомлен')?></a></div>
                        <?endif;?>
                    </td>
                </tr>
            <?endwhile;?>
        </table>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<?/*<script type="text/html" id="form_tmpl">
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
                    <span id="user_to"><?=$user->fullname?></span>
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
                        <div class="bg_form" style="margin-left:64px;"><button id="send_btn"><?=X3::translate('Отправить')?></button></div>
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
        $('#send_message, .answerme').click(function(){
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
            var select = eform.find('[fcselect]');
            if($(this).hasClass('answerme')){
                var uid = (/\/([0-9]+)\.html/).exec($(this).attr('href')).pop();
                select.val(uid);
            }
            select.fcselect({width:602});
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
</script>*/?>