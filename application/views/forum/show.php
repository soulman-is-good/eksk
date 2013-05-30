<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" id="send_message" href="#message/send.html"><?=X3::translate('Написать сообщение')?></a></div>
        </div>
        <h1 title="<?=$theme->title?>"><?=X3_String::create($theme->title)->carefullCut(35)?></h1>
    </div>
    <div class="content">
        <div class="admin-list">
            <a href="/forum/"><i>←</i><?=X3::translate('Вернуться к списку тем');?></a>
            <?foreach($models as $model):
                $user = $users[$model->user_id];
                $m = $model->user_to>0?$users[$model->user_to]:false;
                $parent = false;
                if($model->parent_id>0){
                    $parent = Forum_Message::getByPk($model->parent_id);
                }
                $files = X3::db()->query("SELECT f.id id, f.name name FROM data_uploads f INNER JOIN forum_uploads mu ON mu.file_id=f.id WHERE mu.message_id = ".$model->id);
                ?>
                <div class="message_block" pid="<?=$model->id?>">
                        <div class="inside_block">
                        <div class="left_side">
                                <img width="100" src="<?=$user['avatar']?>">
                        </div>
                        <div class="middle_side">
                                <a href="/user/<?=$model->user_id?>.html"><?=$user['title']?></a>
                                <i><?=I18n::date($model->created_at)?>, <?=date("H:i",$model->created_at)?></i>
                        </div>
                        <div class="right_side">
                            <?if($m!=false):?>
                                <div class="answered-to">
                                    <div class="fleft"><em><?=X3::translate('Ответил пользователю');?></em></div>
                                    <img src="<?=$m['avatar']?>" class="miniava" width="50" alt="" title="<?=addslashes($m['title'])?>" />
                                    <div class="fleft"><a href="/user/<?=$model->user_id?>"><?=$m['title']?></a></div>
                                    <div class="clear">&nbsp;</div>
                                </div>
                            <?endif;?>
                            <?if($parent):?>
                            <div class="quote"><i>&#147;</i><span><?=nl2br(X3_String::create($parent->content)->carefullCut(512))?></span></div>
                            <?endif;?>
                                <?if(X3::user()->search!=null && X3::user()->search['type']=='themes'):?>
                                    <p><?=nl2br(str_ireplace(X3::user()->search['word'],"<b style='color:#922'>".X3::user()->search['word']."</b>",$model->content))?></p>
                                <?else:?>
                                    <p><?=nl2br($model->content)?></p>
                                <?endif;?>
                                <?if(mysql_num_rows($files)):?>
                                <em><?=X3::translate('Прикрепленные файлы')?></em>:
                                <?while($file = mysql_fetch_assoc($files)):?>
                                <span class="file_link"><a href="/uploads/get/file/<?=$file['id']?>"><?=$file['name']?></a></span>
                                <?endwhile;?>
                                <?endif;?>
                                <?if($model->user_id == X3::user()->id || X3::user()->isAdmin()):?>
                                <div class="del"><a href="/forum/delete/message/<?=$model->id?>.html" class="map_link remove"><img src="/images/cross.png" alt="<?=X3::translate('Удалить')?>" title="<?=X3::translate('Удалить')?>" /></a></div>
                                <?endif;?>
                                <?if($model->user_id != X3::user()->id):?>
                                <br/><a data-uid="<?=$model->user_id?>" data-pid="<?=$model->id?>" href="#" class="answer button"><?=X3::translate('Ответить');?></a>
                                <?endif;?>
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
    <form method="post" action="/forum/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form" width="100%">
            <tr id="user_info">
                <td class="label" width="70">
                    <label for="Message[user_to]"><?=Message::getInstance()->fieldName('user_to')?></label>
                </td>
                <td class="field" style="padding:5px 0px">
                    <input id="Message_user_to" type="hidden" name="Message[user_to]"  value="" />
                    <span id="user_to"></span>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="Message[content]"><?=Message::getInstance()->fieldName('content')?></label>
                </td>
                <td class="field">
                    <textarea name="Message[content]" style="width:623px"></textarea>
                    <input type="hidden" value="<?=$theme->id?>" name="Message[forum_id]" id="forum_id" />
                    <input type="hidden" value="" name="Message[parent_id]" id="parent_id" />
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
    var users = <?=  json_encode($users)?>;
    $(function(){
        $('.unread').each(function(){
            //$(this).one('mouseover',function(){
                var self=this;
                $.get('/forum/read/id/'+$(this).attr('pid'),function(){  
                    $(self).removeClass('unread');
                })
            //})
        })
        $('#send_message, .answer').click(function(){
            var eform = $($('#form_tmpl').html());
            if($(this).data('uid')>0){
                eform.find('#Message_user_to').val($(this).data('uid'));
                eform.find('#user_to').html(users[$(this).data('uid')]['title']);
                eform.find('#parent_id').val($(this).data('pid'));
                eform.find('#user_info').css('display','table-row');
            }else{
                eform.find('#Message_user_to').val('');
                eform.find('#user_to').html('');
                eform.find('#user_info').css('display','none');
            }
            var self = $.dialog(eform,'<?=X3::translate('Написать сообщение');?>','no buttons');
            self.setSize(750);
            self.setRelativePosition('center');
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
                        location.reload();
                        //$.dialog(m.message,'<?=X3::translate('Новое сообщение');?>',{callback:function(){this.close()},caption:'Закрыть'});
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
        $('.map_link.remove').each(function(){
            $(this).click(function(){
                if(confirm('<?=X3::translate('Вы уверены?');?>')){
                    var href = $(this).attr('href');
                    var self = this;
                    $.get(href,function(m){
                        $(self).parent().parent().parent().parent().fadeOut(function(){
                            $(this).remove();
                        })
                    })
                }
                return false;
            })
        })
    <?if($goto>0):?>
        $(document).scrollTop($('[pid="<?=$goto?>"]').position().top);
    <?endif;?>
    })
</script>