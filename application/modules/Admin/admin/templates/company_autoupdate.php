<?php

$settings = Company_Settings::get(array('company_id'=>$modules->id),1);
$ctitle = strtolower(X3_String::create($modules->title)->translit());
$ctitle = preg_replace("/[^a-zA-Z0-9_\.\-]/", "", $title);
?>
<style>
    #xls tr:nth-child(odd) td {
        background: #e4e4fc;
    }
    a.info {
        color: #AAAAAA;
        font-size: 85%;
        text-decoration: none;
        border-bottom: 1px dotted #AAAAAA;
    }
    .success{
        background: #d8fae0;
        border: 1px solid #bef5cb;
        color: #55715b;
        font: bold 12px Tahoma, sans-serif;
        padding:10px;
        border-radius: 10px;
        top:120px !important;
    }
</style>
<div class="x3-submenu">
    <?= $modules->menu('autoupdate'); ?>
</div>
<div class="x3-main-content" style="position:relative;">
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle; ?></a> > Обновление (<?=$modules->title?>)</div>
    <div style="color: #55715b;float:right">Последнее обновление: <?=$modules->updated_at>0?date("d.m.Y H:i:s",$modules->updated_at):'<em>не изменялось</em>'?></div>
    <?if(isset($found)):?>
    <div class="success" style="width:300px;position:absolute;right:10px;top:120px">
        <?=($notfound==''?'Все ':'Только ').$found.' '.X3_String::create('товар')->numeral($found,array('товар','товара','товаров')).
            ' '.X3_String::create('найдено')->numeral($found,array('найден','найдено','найдено'));?><br/>
        <?=$notfound?>
    </div>
    <?endif;?>
    <br clear="both" />
    <div id="x3-container">
        <form method="post" enctype="multipart/form-data" id="form">
            <fieldset style="float:left;">
                <legend>Загрузить</legend>
                <table style="width:350px;">
                    <tr>
                        <td style="text-align: right;">из файла:</td>
                        <td style="text-align: left;"><input type="file" name="upload" id="file" /></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">по ссылке:</td>
                        <td style="text-align: left;"><input type="text" name="url" id="url" value="<?=$settings->autoload_url?>" /></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Автообновление:</td>
                        <td style="text-align: left;"><input type="checkbox" name="autoupdate" id="auto" <?=$settings->autoload_auto?'checked':''?> /></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="float:left">
                <legend>Опции</legend>
                <table style="width:350px;">
                    <tr>
                        <td style="text-align: right;">Шаблон ссылки:</td>
                        <td style="text-align: left;"><input type="text" name="shop_url" id="surl" value="<?=$settings->autoload_shopurl?>" /><br/>
                            <a class="info" onclick="$('#surl').val('http://site.kz/catalog/?id=[ID]')">Например, http://site.kz/catalog/?id=[ID]</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">Коэффициент цены:</td>
                        <td style="text-align: left;"><input type="text" value="<?=$settings->autoload_koef?>" name="price_koef" /></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">MD5 для ID клиента:</td>
                        <td style="text-align: left;"><input type="checkbox" name="md5" id="md55" <?=$settings->autoload_md5?'checked':''?> /></td>
                    </tr>
                </table>
            </fieldset>
            <?if($modules->updated_at>0):?>
            <div style="float:right;margin-top:15px"><a id="list" href="/company/listupdates/id/<?=$modules->id?>">Список отчетов</a></div>
            <?endif;?>
            <br style="clear: both" />
            <?if(is_file($settings->last_autoupdate_file)):?>
            <div style="position: absolute; top: 80px; right: 20px;"><a id="list" href="/<?=$settings->last_autoupdate_file?>">Последний загруженный файл</a></div>
            <br style="clear: both" />
            <?endif;?>
            <input type="hidden" name="type" value="<?=$settings->autoload_type?>" id="ftype" />
            <div id="filetype">
                <ul>
                    <li><a href="#xls">Excel</a></li>
                    <li><a href="#csv">CSV</a></li>
                    <li><a href="#xml">XML</a></li>
                    <li style="float:right;background: none;border:none;"><button onclick="saveall();" type="button" >Сохранить</button></li>
                </ul>
                <div id="xls">
                    <table>
                        <tr>
                            <td align="right"><nobr>Столбец Артикула:</nobr></td>
                            <td><select name="prop[xls][articule]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right"><nobr>Столбец ID товара:</nobr></td>
                            <td><select name="prop[xls][id]" style="width:120px"><option value="0" selected>A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец цены:</td>
                            <td><select name="prop[xls][price]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1" selected>B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец URL:</td>
                            <td><select name="prop[xls][url]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2" selected>C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец названия:</td>
                            <td><select name="prop[xls][name]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец группы:</td>
                            <td><select name="prop[xls][group]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец фото:</td>
                            <td><select name="prop[xls][photo]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец описания:</td>
                            <td><select name="prop[xls][desc]" style="width:120px"><option value="-1">Игнорировать<option value="0" selected>A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>                        
                    </table>
                </div>
                <div id="csv">
                    <table>
                        <tr>
                            <td align="right">Разделитель ячеек:</td>
                            <td><input name="prop[csv][delimiter]" id="csv_terminated" style="width:120px" value=";"></td>
                        </tr>
                        <tr>
                            <td align="right"><nobr>Столбец Артикула:</nobr></td>
                        <td><select name="prop[csv][articule]" style="width:120px"><option value="-1" selected>Игорировать<option value="0">1<option value="1">2<option value="2">3<option value="3">4<option value="4">5<option value="5">6<option value="6">7<option value="7">8<option value="8">9<option value="9">10<option value="10">11<option value="11">12<option value="12">13<option value="13">14<option value="14">15<option value="15">16<option value="16">17<option value="17">18<option value="18">19<option value="19">20</select></td>
                        </tr>
                        <tr>
                            <td align="right"><nobr>Столбец ID товара:</nobr></td>
                        <td><select name="prop[csv][id]" style="width:120px"><option value="0">1<option value="1" selected>2<option value="2">3<option value="3">4<option value="4">5<option value="5">6<option value="6">7<option value="7">8<option value="8">9<option value="9">10<option value="10">11<option value="11">12<option value="12">13<option value="13">14<option value="14">15<option value="15">16<option value="16">17<option value="17">18<option value="18">19<option value="19">20</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец цены:</td>
                            <td><select name="prop[csv][price]" style="width:120px"><option value="-1">Игнорировать<option value="0">1<option value="1">2<option value="2" selected>3<option value="3">4<option value="4">5<option value="5">6<option value="6">7<option value="7">8<option value="8">9<option value="9">10<option value="10">11<option value="11">12<option value="12">13<option value="13">14<option value="14">15<option value="15">16<option value="16">17<option value="17">18<option value="18">19<option value="19">20</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец URL:</td>
                            <td><select name="prop[csv][url]" style="width:120px"><option value="-1">Игнорировать<option value="0">1<option value="1">2<option value="2" >3<option value="3">4<option value="4">5<option value="5">6<option value="6">7<option value="7">8<option value="8">9<option value="9">10<option value="10">11<option value="11">12<option value="12">13<option value="13">14<option value="14">15<option value="15">16<option value="16">17<option value="17">18<option value="18">19<option value="19">20</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец Названия:</td>
                            <td><select name="prop[csv][name]" style="width:120px"><option value="-1">Игнорировать<option value="0">1<option value="1">2<option value="2">3<option value="3">4<option value="4">5<option value="5">6<option value="6">7<option value="7">8<option value="8">9<option value="9">10<option value="10">11<option value="11">12<option value="12">13<option value="13">14<option value="14">15<option value="15">16<option value="16">17<option value="17">18<option value="18">19<option value="19">20</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец фото:</td>
                            <td><input name="prop[csv][photo]" style="width:120px" value="0"></td>
                        </tr>                        
                    </table>                    
                </div>
                <div id="xml">
                    <table>
                        <tr>
                            <td align="right">Тег контейнера товара:</td>
                            <td><input type="Text" name="prop[xml][item]" style="width:120px" value="item"></td>
                        </tr>
                        <tr>
                            <td align="right">Тег Артикула:</td>
                            <td><input type="Text" name="prop[xml][articule]" style="width:120px" value=""></td>
                        </tr>
                        <tr>
                            <td align="right">Тег ID:</td>
                            <td><input type="Text" name="prop[xml][id]" style="width:120px" value="id"></td>
                        </tr>
                        <tr>
                            <td align="right">Тег цены:</td>
                            <td><input type="Text" name="prop[xml][price]" style="width:120px" value="price"></td>
                        </tr>
                        <tr>
                            <td align="right">Тег URL:</td>
                            <td><input type="Text" name="prop[xml][url]" style="width:120px" value="url"></td>
                        </tr>
                        <tr>
                            <td align="right">Тег Названия:</td>
                            <td><input type="Text" name="prop[xml][name]" style="width:120px" value=""></td>
                        </tr>
                        <tr>
                            <td align="right">Тег фото:</td>
                            <td><input name="prop[xml][photo]" style="width:120px" value="0"></td>
                        </tr>
                        <tr>
                            <td align="right">Тег описания:</td>
                            <td><input type="Text" name="prop[xml][desc]" style="width:120px" value=""></td>
                        </tr>                        
                    </table>
                </div>                
            </div>
            <br/>
            <hr/>
            <button id="submit" type="submit">Обновить</button>
        </form>
    </div>
</div>
<script>
    var iframe = $('<iframe />').attr({'name':'frm','id':'frm'}).css({'width':'0px','height':'0px','position':'absolute','visibility':'hidden'}).load(function(){
        var res = eval('[' + $(this).contents().find('body').html() + ']');
        res = res.pop();
        if(res == null) return;
        $('#form').removeAttr('target').removeAttr('action');
        $('#jqGrid').jqGrid({
            datatype:'local',
            height:400      
        });
    }).insertAfter($('#form'));    
    $('#file').change(function(){
        //$('#form').attr({'target':'frm','action':'/company/loadup'}).submit();
    })
    
    $("#list").click(function(){
        var h = $(this).attr('href');
        var list = $.loadJSON(h,false);
        var ul = $('<ul />');
        for(i in list){
            var li = $('<li />');
            li.append($('<a />').attr({'href':list[i],'target':'_blank'}).html(list[i].split('/').pop()))
            ul.append(li);
        }
        ul.dialog({title:'Список отчетов',buttons:{'Закрыть':function(){$(this).dialog('close')},
                'Очистить':function(){$(this).dialog('close');$.get(h+'/empty/all')}}});
        return false;
    });
    
    $(document).ready(function(){
        var props = <?=  json_encode($modules->update_props())?>;
        console.log(props)
        for(i in props){
            for(j in props[i]){
                $("[name='prop["+i+"]["+j+"]']").val(props[i][j]);
            }
        }
        $("#filetype").tabs();
        $("#filetype").tabs('select','#'+$('#ftype').val());
        $("#filetype").bind('tabsselect',function(e,ui){var a = $(ui.tab).attr('href').replace('#','');$('#ftype').val(a)});
    })
    function check_stat(){   
        $.post('/uploads/autoload-<?=$modules->id?>.stat',function(m){
            if(m!=null){
                if(typeof $('#status')[0] == 'undefined'){
                    $('<fieldset />').css({'float':'left','position':'relative'}).append($('<legend />').html('Статус загрузки'))
                    .append($('<div />').attr({'id':'status'})).insertAfter($('fieldset:last'));
                }
                if(m.status != 'success' && m.status != 'error'){
                    if(m.status == 'loading'){
                        $('#status').html('Поиск ('+m.message+'%)')
                    }else{
                        $('#status').html(m.message);
                    }
                    setTimeout(function(){check_stat();},1000)
                }else if(m.status == 'success'){
                    if(m.message==''){
                        $('#status').html('Обновление завершено. Ничего не найдено.<br/>');
                    }else{
                        $('#status').html('Обновление завершено:<br/><a href="/'+m.message+'" target="_blank">'+m.message.split('/').pop()+'</a>');
                    }
                    $('#status').append($('<a href="#close"><img src="/images/cross.png" width="16" /></a>').css({'position':'absolute','right':'0px','top':'0px'}).click(
                        function(){
                            $(this).parent().parent().remove();
                            $('.x3-main-content .success').remove();
                            $.post('/admin_Tools/deletefile',{'filename':'uploads/autoload-<?=$modules->id?>.stat'});
                        }
                    )); 
                    $('.x3-main-content').prepend(m.additional);
                }else{
                    $('#status').html('Возникла ошибка: <br /><span style="color:#f00">'+m.message+'</span>');
                }
            }
        },'json').error(function(e){
            console.log(e);
            return false;
        })
    }
    
    function saveall(){
        $.post('/company/saveautoup/id/<?=$modules->id?>',$('#form').serialize(),function(){
            $.growl('Сохранено!','ok');
        })
    }
    
    check_stat();
</script>