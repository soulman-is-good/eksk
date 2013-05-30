<?php
$title = X3::translate('Создание отчета');
$pk = 'id';
$errors = $model->getTable()->getErrors();
$user = User::getByPk($model->user_id);
$form = new Form($model);
$options2 = array(
    'admin'=>X3::translate('Администратор'),
    'ksk'=>X3::translate('КСК'),
    'user'=>X3::translate('Жилец'),
);
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
        <table class="eksk-form login-form">
            <?if(X3::user()->isAdmin() && X3::user()->id != $model->user_id && !$model->getTable()->getIsNewRecord()):?>
            <tr>
                <td class="label">
                    <label><?=X3::translate('Создатель');?></label>
                </td>
                <td class="field" colspan="2">
                    <?=$user->fullname?> (<?=$options2[$user->role]?>)
                </td>
            </tr>
            <?endif;?>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('title')?></label>
                </td>
                <td class="field">
                    <div class="wrapperEx inline-block"><?=$form->textarea('title')?></div>
                </td>
                <td class="error">
                    <?=$form->error('title')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$model->fieldName('file_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapperEx inline-block">
                        <?=X3_Html::form_tag('input',array('type'=>'file','name'=>'file','id'=>'file_id'))?>
                    </div>
                        <?php
                            if(!$model->getTable()->getIsNewRecord() && NULL!=($file = Uploads::getByPk($model->file_id))){
                                echo X3_Html::open_tag('div', array());
                                echo $form->hidden('file_id');//X3_Html::form_tag('input',array('type'=>'hidden','name'=>'User_Report[file_id]','id'=>'file_id_src','val'));
                                echo '<a href="/uploads/get/file/' . $model->file_id . '" target="_blank">'.$file->name.'</a>';
                                echo X3_Html::close_tag('div');
                            }
                        ?>
                </td>
                <td class="error">
                    <?=$form->error('file_id')?>
                </td>
            </tr>
            <?=$form->renderPartial(array('created_at'))?>
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
                    <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('class'=>'region_id','id'=>'User_Report_region_id','name'=>'User_Report[region_id]','rid'=>$model->region_id,'fcselect'=>'autocomplete','data-width'=>'345'))?></div>
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
                    <div class="wrapper inline-block"><select fcselect data-width="345" name="User_Report[house]" id="User_Report_house" hid="<?=addslashes($model->house)?>"></select></div>
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
                    <div class="wrapper inline-block"><select fcselect data-width="345" name="User_Report[flat]" id="User_Report_flat" hid="<?=addslashes($model->flat)?>"></select></div>
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
                    <div class="wrapper inline-block"><button type="submit" name="public"><?=X3::translate('Создать и опубликовать');?></button></div>
                    <?else:?>
                    <div class="wrapper inline-block"><button type="submit"><?=X3::translate('Сохранить');?></button></div>
                    <div class="wrapper inline-block"><button type="submit" name="public"><?=X3::translate('Опубликовать');?></button></div>
                    <?endif;?>
                </td>
            </tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $(function(){
        //Address
        //TODO: CACHING
        $('.city_id').live('change',function(){
            var city_id = $(this).val();
            var C = this;
            var R = $('#User_Report_region_id');
            var rid = R.attr('rid');
            if(city_id>0)
            $.get('/city/region.html',{id:city_id},function(m){
                R.html('');
                var o = $('<option />').attr({'value':'0'}).data('houses',[]).html('<?=X3::translate('Все');?>');
                R.append(o);
                for(i in m){
                    var o = $('<option />').attr({'value':m[i].id}).data('houses',m[i].houses).html(m[i].title);
                    if(m[i].id == rid)
                        o.attr('selected',true);
                    $('#User_Report_region_id').append(o);
                }
                R.data('fcselect').redraw()
                $(C).parent().parent().parent().parent().find('.region_id').change();
            },'json')
            else {
                R.html('');
                var o = $('<option />').attr({'value':'0'}).data('houses',[]).html('<?=X3::translate('Все');?>');
                R.append(o);
                R.data('fcselect').redraw()
                $(C).parent().parent().parent().parent().find('.region_id').change();
            }
        })
        $('.region_id').live('change',function(){
            var H = $('#User_Report_house');
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
            $('#User_Report_house').change();
        });
        $('#User_Report_house').change(function(){
            var H = $('#User_Report_flat');
            var flat = H.attr('hid');
            $('[fcselect]').attr('disabled',true);
            H.html('');
            $.get('/warning/flats',{rid:$('#User_Report_region_id').val(),house:$('#User_Report_house').val(),cid:$('#User_Report_city_id').val()},function(m){
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
                $('[fcselect]').attr('disabled',false);
            },'json')
        });
        $('.city_id').change();
        
        //$('#User_Report_created_at').datepicker('option','minDate',0);
    })
</script>