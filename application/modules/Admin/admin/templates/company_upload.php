<?php
    $G = null;
    $groups = Shop_Group::get(array('@order'=>array('title')),false);
    $groupid = (int)$_GET['group'];
    $gs = '';
    foreach($groups as $group){
            if($groupid==$group->id){
                $gs .= X3_Html::form_tag('option', array('value'=>$group->id,'%content'=>$group->title,'selected'=>'selected'));
                $G = $group;
            }else
                $gs .= X3_Html::form_tag('option', array('value'=>$group->id,'%content'=>$group->title));
    }
    $gs = '<select style="font-size: 14px;" id="group_select"><option vlaue="0">Выберите группу</option>'.$gs.'</select>';
?>
<div class="x3-submenu">
    <?=$modules->menu('upload');?>
</div>
<div class="x3-main-content">
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle; ?></a> > Поиск в интернете <?=$gs?></div>
    <div id="x3-container">
        <?if($G!==null):?>
        <br/>
        <hr/>
        <form method="post" enctype="multipart/form-data" id="uploads">
            <?if(!isset($upload_data)):?>
            Загрузить файл: <input type="file" name="file" />
            <button type="submit">Загрузить</button>
            <br/>
            <br/>
            <?endif;?>
        <div id="tabs">
            <ul>
                <li><a href="#settings">Настройки</a></li>
                <li><a href="#rules">Правила</a></li>
                <li><a href="#data">Данные</a></li>
                <li style="float:right;background: none;border:none;"><button type="button" onclick="saveall();">Сохранить</button></li>
            </ul>
            <div id="settings">
            <?php
                $_props = Shop_Properties::get(array('@order'=>'weight','@condition'=>array('group_id'=>$G->id)));
                $cprops = Company_Settings::get(array('company_id'=>$modules->id),1);
                $rules = array();
                $adds = array();
                if($cprops===null)
                    $cprops = array();
                else
                    $cprops = $cprops->value;
                if(isset($cprops["group-$G->id"])){
                    $rules = $cprops["group-$G->id"]['rules'];
                    $adds = $cprops["group-$G->id"]['adds'];
                    $cprops = $cprops["group-$G->id"]['props'];
                }?>
                <table width="500" cellpadding="5">
                    <tr>
                        <td style="text-align: right">ID</td>
                        <td style="text-align: left"><input type="text" name="props[client_id]" value="<?=isset($cprops['client_id'])?$cprops['client_id']:''?>" />
                            <select name="props[client_func]">
                                <option value="">нет</option>
                                <option <?=(!empty($cprops['client_func']))?'selected':''?> value="md5">MD5</option>
                            </select>
                        </td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Колонка производителя</td>
                        <td style="text-align: left"><input type="text" name="props[manufacturer]" value="<?=isset($cprops['manufacturer'])?$cprops['manufacturer']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Артикул</td>
                        <td style="text-align: left"><input type="text" name="props[articule]" value="<?=isset($cprops['articule'])?$cprops['articule']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Модель</td>
                        <td style="text-align: left"><input type="text" name="props[title]" value="<?=isset($cprops['title'])?$cprops['title']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Цена</td>
                        <td style="text-align: left"><input type="text" name="props[price]" value="<?=isset($cprops['price'])?$cprops['price']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">URL</td>
                        <td style="text-align: left"><input type="text" name="props[url]" value="<?=isset($cprops['url'])?$cprops['url']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Фото</td>
                        <td style="text-align: left"><input type="text" name="props[image]" value="<?=isset($cprops['image'])?$cprops['image']:''?>" /></td>
                        <td style="text-align: left">&nbsp;</td>
                    </tr>
            <?
            $_tmpProps = array('manufacturer'=>'Производитель','articule'=>'Артикул','title'=>'Модель','price'=>'Цена','client_id'=>'ID','url'=>'URL','image'=>'Фото');
            $_tmpRules = array();
            foreach ($_tmpProps as $k=>$p)
                if(!isset($cprops[$k]) && $cprops[$k]!='') unset($_tmpProps[$k]);
            foreach($_props as $p):
                if(isset($cprops[$p->name]) && $cprops[$p->name]!='')
                    $_tmpProps[$p->name] = $p->label;
            ?>
                    <tr>
                        <td style="text-align: right"><?=$p->label?></td>
                        <td style="text-align: left"><input value="<?=(isset($cprops[$p->name])?$cprops[$p->name]:'')?>" name="props[<?=$p->name?>]" type="text" /></td>
                        <td style="text-align: left"><input type="checkbox" name="adds[<?=$p-name?>]" <?=(isset($adds[$p->name])?'checked':'')?> /></td>
                    </tr>
            <?endforeach;?>
                </table>
            </div>
            <div id="rules">
                <?
                foreach($rules as $a=>$rule):?>
                <fieldset id="rule-<?=$a?>">
                    <legend>Колонка <?=$a?></legend>
                    <table>
                        <?foreach ($rule as $k=>$v):
                            $_tmpRules[$k]=$v;
                        ?>
                        <tr>
                            <td><?=$_tmpProps[$k]?></td>
                            <td><input name="rules[<?=$a?>][<?=$k?>]" value="<?=$v?>" type="text" /></td>
                        </tr>
                        <?endforeach;?>
                    </table>
                </fieldset>
                <?endforeach;?>
                <br clear="left" />
            </div>
            <div id="data" style="width:1000px;position: relative;overflow: auto">
                <div class="ui-corner-all" style="padding:7px;margin:10px;border:1px solid #555;">
                <button id="play" type="button" primary-icon="ui-icon-play" onclick="startUp();">Начать загрузку</button>
                <button id="stop" type="button" primary-icon="ui-icon-stop" disabled onclick="stopUp();">Остановить загрузку</button>
                <button id="delete" type="button" primary-icon="ui-icon-trash" onclick="deleteUp()">Удалить загрузку</button>
                </div>
                <div id="progress" style="height:8px;font-size: 1px;margin-bottom:10px"></div>
                <table id="res"></table>
            </div>
        </div>
        </form>
        <?endif;?>
    </div>
</div>
<script>
    var ID = <?=$modules->id?>;
    var GID = <?=$groupid?>;
    var rl = <?=  json_encode($_tmpRules)?>;
    $("#group_select").change(function(){
        location.href = '/admin/company/upload/'+ID+'/group/'+$(this).val();
        //$.loadJSON('/admin/company/upload/'+ID)
    })
    if($("#rules fieldset").length>0)
        $("#tabs").tabs({'disabled':[2]});
    else
        $("#tabs").tabs({'disabled':[1,2]});
    
    $("#settings input[type='text']").change(function(){
        generate_rules();
    })
    function saveall(){
        $.post('/company/savesettings/cid/'+ID+'/gid/'+GID,$("#uploads").serialize(),function(m){
            if(m=='OK')
                $.growl('Сохранено!','ok');
            else{
                $.growl('Ошибка при сохранении');
                console.log(m);
            }
        }).error(function(m){
                $.growl('Ошибка при сохранении');
                console.log(m);
        })
    }
    function generate_rules(){
        var letters = {};
        $("#settings input[type='text']").each(function(){
            var L = $(this).val().toUpperCase();
            if(L.replace(/[\s]*/g,'') == '') 
                return;
            if(typeof letters[L] == 'undefined')
                letters[L] = new Array();
            letters[L].push($(this));
        })
        var trig = false;
        for(i in letters){
            if(typeof $("#rule-"+i)[0] != 'undefined'){
                $("#rule-"+i).remove();
            }            
            if(letters[i].length>1){
                trig = true;
                var wrapper = $('<table />');
                for(j in letters[i]){
                    var el = letters[i][j];
                    var tr = $('<tr />');
                    var name = el.attr('name').replace('props[','').replace(']','');
                    tr.append($('<td />').append(el.parent().siblings('td').text()))
                    tr.append($('<td />').append(
                        $('<input />').attr({'name':'rules['+i+']['+name+']','value':(typeof rl[name]=='undefined')?'.*':rl[name]})
                    ));
                    wrapper.append(tr);
                }
                $("#rules").append($('<fieldset />').attr({'id':'rule-'+i}).append('<legend>Колонка '+i+'</legend>').append(wrapper));
            }
            if(trig){
                $("#tabs").tabs('enable',1);
            }else
                $("#tabs").tabs('disable',1);
        }
    }
    
    <?if(isset($upload_data)):
        $status = json_decode(file_get_contents("uploads/Company/upload-$groupid-$modules->id.status"),1);
?>
        var goesOn = false;
        function deleteUp(){
            if(confirm('Вы уверены?')){
                location.href = '/admin/company/upload/'+ID+'/group/'+GID+'/delete/all';
            }
        }
        function startUp(){
            $("#play").button('option','disabled',true);
            $("#stop").button('option','disabled',false);
            $("#delete").button('option','disabled',true);
            $.get('/company/job/upload/'+ID+'/group/'+GID+'/status/play',function(){
                goesOn=true;
                getStat();
            })
        }
        function stopUp(){
            goesOn=false;
            $("#play").button('option','disabled',false);
            $("#delete").button('option','disabled',false);
            $("#stop").button('option','disabled',true);
            $.get('/company/job/upload/'+ID+'/group/'+GID+'/status/stop',function(){
            })
        }
        $("#tabs").tabs('enable',2);
        $("#tabs").tabs('select',2);
        var data = <?=$upload_data?>;
        var pl = <?=  json_encode($_tmpProps)?>;
        var found = <?=  json_encode($status['found'])?>;
        var labels = [];
        var cols = [];
        var lastsel = 0;
        var j = 0;
        for(i in pl){
            labels.push(pl[i]);
            if(found.indexOf(j)==-1);
            cols.push({name:i,index:i,width:100,sortable:false,editable:true});
            j++;
        }
        jQuery("#res").jqGrid({ 
            datatype: "local", 
            height: 250, 
            colNames:labels, 
            colModel:cols,
            multiselect: false,
            viewrecords: true,
            onSelectRow: function(id){ 
                if(id && id!==lastsel){ 
                    jQuery('#res').jqGrid('restoreRow',lastsel); 
                    jQuery('#res').jqGrid('editRow',id,true); 
                    lastsel=id; 
                } 
            },
            editurl: "/company/updateupload/cid/"+ID+"/gid/"+GID,
            caption: "Загружаемый прайс" 
        });
        for(var i=0;i<=data.length;i++) {
            jQuery("#res").jqGrid('addRowData',i+1,data[i]);
        }
        function getStat(){
            $.loadJSON('/uploads/Company/upload-'+GID+'-'+ID+'.status',function(m){
                if(m.status=='stop'){
                    goesOn = false;
                    $("#play").button('option','disabled',false);
                    $("#delete").button('option','disabled',false);
                    $("#stop").button('option','disabled',true);
                }else{
                    for(k in m.found){
                        jQuery("#res").jqGrid('delGridRow',m.found[k],{reloadAfterSubmit:false});
                    }
                    goesOn = true;
                    $("#play").button('option','disabled',true);
                    $("#stop").button('option','disabled',false);
                    $("#delete").button('option','disabled',true);
                }
                $("#progress").progressbar('option','value',m.percent*1)
            })
            if(goesOn)
            setTimeout(function(){getStat();},1000)
        }
        <?if($status['status']!='stop'):?>
        goesOn=true;
        getStat();
        <?endif;?>
        $("#progress").progressbar({value:<?=$status['percent']?>})
    <?endif;?>
</script>