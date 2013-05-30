<?php
$title = $model->id>0?X3::translate('Редактирование обсуждения'):X3::translate('Создание обсуждения');
$pk = 'id';
$errors = $model->getTable()->getErrors();
$files = '';
$F = array();
if($message->id>0){
    $fs = X3::db()->fetchAll("SELECT fu.file_id, u.name FROM forum_uploads fu INNER JOIN data_uploads u ON fu.file_id=u.id WHERE message_id=$message->id");
    if(!empty($fs)){
        $tmp = array();
        foreach($fs as $fl){
            $tmp[] = $fl['file_id'];
            $F[$fl['file_id']] = $fl['name'];
        }
        $files = implode(',',$tmp);
        unset($tmp);
    }
}
$form = new Form($model);
?>
<div class="eksk-wnd">
    <div class="head"><h1><?=$title?></h1></div>
    <div class="content">
        <?if(!empty($errors)):?>
        <div class="errors">
            <ul>
                <?foreach($errors as $errs):?>
                    <?foreach($errs as $error):?>
                <li><?=$error?></li>
                    <?endforeach;?>
                <?endforeach;?>
            </ul>
        </div>
        <?endif;?>
        <?=$form->start()?>
        <?if(!$model->getTable()->getIsNewRecord()):?>
        <?=$form->hidden($pk)?>
        <?endif;?>
        <?if(!$message->getTable()->getIsNewRecord()):?>
        <?=  X3_Html::form_tag('input', array('type'=>'hidden','value'=>$message->id,'name'=>'Message[id]'))?>
        <?endif;?>
        <table class="eksk-form login-form">
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('title')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=$form->input('title')?></div>
                </td>
                <td class="error">
                    <?=$form->error('title')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=X3::translate('Сообщение')?></label>
                </td>
                <td class="field">
                    <div class="wrapperEx inline-block"><?=X3_Html::form_tag('textarea', array('name'=>'Message[content]','%content'=>$message->content,'class'=>'content','rows'=>'7'))?></div>
                </td>
                <td class="error"></td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=X3::translate('Прикрепленные файлы')?>:</label>
                </td>
                <td class="field">
                    <div style="position:relative;">
                        <a href="#" class="map_link" style="display: inline-block"><span><?=X3::translate('Прикрепить файл')?></span></a>
                        <input type="hidden" id="file_trig" name="file_trigger" value="0" />
                        <input type="hidden" id="files" name="Message[files]" value="<?=$files?>" />
                        <input type="file" id="file" name="file" class="file" size="1" />
                        <iframe name="for_files" id="for_files" class="upload"></iframe>
                    </div>
                    <div class="hr" style="margin:10px 0;">&nbsp;</div>
                    <div id="file_list">
                        <?if(empty($F)):?>
                        <em><?=X3::translate('Нет файлов');?></em>
                        <?else:?>
                        <?foreach($F as $k=>$fl):?>
                        <span class="file_link"><a target="_blank" href="/uploads/get/file/<?=$k?>"><?=$fl?></a><a data-fid="<?=$k?>" fileremove class="red_cross" href="#"><img width="7" height="" src="/images/zeropic.png" /></a></span>
                        <?endforeach;?>
                        <?endif;?>
                    </div>
                </td>
                <td class="error"></td>
            </tr>
        </table>
        <div class="hr">&nbsp;</div>
        <table class="eksk-form login-form">
            <tr>
                <td class="label">
                    &nbsp;
                </td>
                <td class="field">
                    <h3><?=X3::translate('Целевая аудитория');?></h3>
                </td>
                <td class="error">
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('city_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=$form->select('city_id',array('class'=>'city_id','fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?=$form->error('city_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('region_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('class'=>'region_id','id'=>'Forum_region_id','name'=>'Forum[region_id]','rid'=>$model->region_id,'fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?=$form->error('region_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('house')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><select fcselect data-width="345" name="Forum[house]" id="Forum_house" hid="<?=addslashes($model->house)?>"></select></div>
                </td>
                <td class="error">
                    <?=$form->error('house')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('flat')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><select fcselect data-width="345" name="Forum[flat]" id="Forum_flat" hid="<?=addslashes($model->flat)?>"></select></div>
                </td>
                <td class="error">
                    <?=$form->error('flat')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    &nbsp;
                </td>
                <td class="field" colspan="2">
                    <?if($model->getTable()->getIsNewRecord()):?>
                    <div class="wrapper inline-block"><button type="submit"><?=X3::translate('Создать');?></button></div>
                    <div class="wrapper inline-block"><button type="submit" name="public" value="1"><?=X3::translate('Создать и опубликовать');?></button></div>
                    <?else:?>
                    <div class="wrapper inline-block"><button type="submit"><?=X3::translate('Сохранить');?></button></div>
                    <div class="wrapper inline-block"><button type="submit" name="public" value="1"><?=X3::translate('Опубликовать');?></button></div>
                    <?endif;?>
                </td>
            </tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    var file_tpl = '<span class="file_link"><a filetitle href="#"></a><a fileremove class="red_cross" href="#"><img width="7" height="" src="/images/zeropic.png" /></a></span>';
    var no_files = '<em><?=X3::translate('Нет файлов');?></em>';
    $(function(){
        $('#file').change(function(){
            $('#file_trig').val('1');
            $(this).parents('form').attr({'target':'for_files'}).submit();
            $.loader();
        })
        $('#for_files').load(function(){
            $('#file_trig').val('0').parents('form').removeAttr('target');
            var html = $(this).contents().find('body').html();
            if(html!='' && html != null){
                var json = eval('(' + html + ')');
                if(typeof $.rusWindows['@loader'] !== 'undefined')
                    $.loader();
                if(json.status == 'ok'){
                    var file = $(file_tpl);
                    file.find('[filetitle]').html(json.message.filename)
                        .attr({'target':'_blank','href':'/uploads/get/file/'+json.message.id});
                    file.find('[fileremove]').data('fid',json.message.id);
                    var files = $('#files').val().split(',');
                    files.push(json.message.id);
                    $('#files').val(files.join(','));
                    if(typeof $('#file_list em')[0] != 'undefined')
                        $('#file_list em').remove();
                    $('#file_list').append(file);
                }else{
                    alert(json.message);
                }
            }
        })
        //Address
        $('.city_id').live('change',function(){
            var city_id = $(this).val();
            var C = this;
            $.get('/city/region.html',{id:city_id},function(m){
                var R = $('#Forum_region_id');
                var rid = R.attr('rid');
                R.html('');
                var o = $('<option />').attr({'value':'0'}).data('houses',[]).html('<?=X3::translate('Все');?>');
                $('#Forum_region_id').append(o);
                for(i in m){
                    var o = $('<option />').attr({'value':m[i].id}).data('houses',m[i].houses).html(m[i].title);
                    if(m[i].id == rid)
                        o.attr('selected',true);
                    $('#Forum_region_id').append(o);
                }
                R.data('fcselect').redraw()
                $(C).parent().parent().parent().parent().find('.region_id').change();
            },'json')
        })
        $('.region_id').live('change',function(){
            var H = $('#Forum_house');
            var house = H.attr('hid');
            H.html('');
            var m = $(this).children(':selected').data('houses');
            var o = $('<option />').attr({'value':'0'}).html('<?=X3::translate('Все');?>');
            H.append(o);
            for(i in m){
                var o = $('<option />').attr({'value':m[i]}).html(m[i]);
                if(m[i] == house)
                    o.attr('selected',true);
                H.append(o);
            }
            H.data('fcselect').redraw();
            $('#Forum_house').change();
        });
        $('#Forum_house').change(function(){
            var H = $('#Forum_flat');
            var flat = H.attr('hid');
            H.html('');
            $.get('/forum/flats',{rid:$('#Forum_region_id').val(),house:$('#Forum_house').val(),cid:$('#Forum_city_id').val()},function(m){
                var o = $('<option />').attr({'value':'0'}).html('<?=X3::translate('Все');?>');
                H.append(o);
                for(i in m){
                    if(m[i] == '') continue;
                    var o = $('<option />').attr({'value':m[i]}).html(m[i]);
                    if(m[i] == flat)
                        o.attr('selected',true);
                    H.append(o);
                }
                H.data('fcselect').redraw();
            },'json')
        });
        $('.city_id').change();
        
        $('[fileremove]').live('click',function(){
                        var files = $('#files').val().split(',').map(function(item,i){return item=item.replace(/\s/g,'');});
                        for(i in files){
                            if(files[i] == $(this).data('fid')){
                                files.splice(i, 1);
                                break;
                            }
                        }
                        if(files.length==0 || (files.length == 1 && files[0]=="")){
                            $('#file_list').html(no_files);
                            $('#files').val('');
                        }else
                            $('#files').val(files.join(','));
                        $(this).parent().fadeOut(function(){$(this).remove()});
                    })
    })
</script>