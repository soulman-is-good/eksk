<style>
    .x3-errors{
        color:#FF0000;
    }
    .holder .service {
        margin:5px;
        border:1px solid #333300;
        border-radius:5px;
        height:24px;
        width:300px;
        clear:right;
    }
    #service_holder .service{
        background: #cc9900;
    }
    #groups_holder .service{
        background: #99ffcc;
    }
    .holder .service .cont {
        padding:4px;
        font:bold 12px Verdana;
    }
    .holder .service .block {
        float:right;
        border-left: 2px dotted #fff;
        border-radius:0 5px 5px 0;
        background: #000;
        padding:2px 2px 4px 4px;
    }
    .shop {
        border:1px solid black;
        width:380px;
        top:-15px;
        background:#fff;
        position:absolute;
        z-index:0;
        overflow:hidden;
        height:200px;
        padding:15px;
    }
    .shop .overlay{
        position: absolute;
        height:100%;
        left:0px;top:0px;
        width:100%;
        background: #99ffcc;
        z-index:2;
        opacity:0.7;
        cursor:pointer;
    }
    .shop.active {
        position:relative;
        z-index:1;
        height:100%;
        overflow: visible;
        top:0px;
    }
    .shop.active .overlay{
        -moz-animation: linear;
        opacity:0;
        display:none;
    }
    
</style>
<?if($subaction!='add'):
    
    
////////////////////////////////
//Bills
////////////////////////////////
    $bill = Company_Bill::get(array('@condition'=>array('company_id'=>$modules->id),'@order'=>'type, ends_at DESC'),0,null,1);
?>
<div class="x3-submenu">
    <?=$modules->menu('edit');?>
</div>
<?endif;?>
<div class="x3-main-content">
<?/*STANDART TEMPLATE*/
if($modules instanceOf X3_Module_Table):
$form = new Form($modules);
//$cities = explode("\n",SysSettings::getValue('Cities','content','Города',null,"Алматы\n Астана\n Караганда"));
$cities = Region::get(array(),0,null,true);
$addreses = new Address();
?>
<div id="x3-buttons" x3-layout="buttons" style="float:right">
    <?if($subaction == 'edit'):?>
    <a href="/admin/company/delete/<?=$modules->id?>" class="x3-button red">Удалить</a>
    <?endif;?>
</div>
<div id="x3-header" x3-layout="header"><a href="/admin/<?=$action?>"><?=$moduleTitle;?></a> > <?if($subaction=='add'):?>Добавить<?else:?>Редактировать<?endif;?></div>
<div class="x3-paginator" x3-layout="paginator"></div>
<div class="x3-functional" x3-layout="functional"></div>
<div class="x3-main-content" style="clear:left">
<div id="tabs">
    <ul>
        <li><a href="#common">Общие</a></li>
        <li><a href="#seo">SEO</a></li>
        <li><a href="#addresses">Адреса</a></li>
        <li><a href="#services">Услуги</a></li>
        <?if(count($bill)>0):?>
        <li><a href="#bills">Счета</a></li>
        <?endif;?>
    </ul>
<?=$form->start(array('action'=>"/admin/save{$action}"));?>
<div id="common">
<?
$errs = $modules->table->getErrors();
if(X3::db()->getErrors()!='' || !empty($errs)):?>
    <div class="x3-errors">
        <ul>
            <?foreach($errs as $err)
                foreach($err as $er):?>
            <li><?=$er?></li>
            <?endforeach;?>
            <li><?=X3::db()->getErrors()?></li>
        </ul>
    </div>
<?endif;?>
<?if(is_file("uploads/Company/$modules->image")):?>
<img src="/uploads/Company/<?=$modules->image?>" style="display:block;" />
<?endif;?>
<?
if(!$modules->table->getIsNewRecord()){
    echo $form->hidden($modules->table->getPK());
    $addreses = Address::get(array('company_id'=>$modules->id));
}
?>
<table class="formtable">
<?=$form->renderPartial(array('title','site','delivery','isfree','image','login'));?>
<tr><td>Пароль</td><td><input type="text" value="<?=$modules->password?>" name="Company[password]" id="Company_password"></td><td>&nbsp;</td></tr>    
<?=$form->renderPartial(array('email_private','text','servicetext','status','weight'));?>
</table>
<table>
<tr><td colspan="3"><?=X3_Html::form_tag('button',array('%content'=>'Сохранить','type'=>'submit'))?></td></tr>
</table>    
</div>
<div id="seo">
<table class="formtable" width="100%">
    <tr id="<?=$class?>-metatitle">
        <td class="fieldname"><?=$modules->fieldName('metatitle')?></td>
        <td class="field"><?=$form->input('metatitle',array('style'=>'width:90%'))?></td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="<?=$class?>-metakeywords">
        <td class="fieldname"><?=$modules->fieldName('metakeywords')?></td>
        <td class="field"><?=$form->textarea('metakeywords',array('rows'=>2,'style'=>'width:90%'))?></td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="<?=$class?>-metadescription">
        <td class="fieldname"><?=$modules->fieldName('metadescription')?></td>
        <td class="field"><?=$form->textarea('metadescription',array('rows'=>3,'style'=>'width:90%'))?></td>
        <td class="error">&nbsp;</td>
    </tr>
<tr><td colspan="3"><?=X3_Html::form_tag('button',array('%content'=>'Сохранить','type'=>'submit'))?></td></tr>
</table>
</div>    
<div id="addresses">
    <fieldset style="border:1px solid #666666;border-radius: 5px;padding:10px;poition:relative;">
        <legend style="padding:5px;margin-left:10px;font-weight: bold;color: #666666">Адреса</legend>
        <div style="position:relative;height:100%">
        <? foreach ($addreses as $i=>$address): ?>
        <div class="shop shop<?=$i?> <?=($i==0)?'active':''?>" style="left:<?=$i*20?>px;">
        <div class="overlay">&nbsp;</div>
<?
    if(!$address->table->getIsNewRecord()){
        echo X3_Html::form_tag('input', array('type'=>'hidden','name'=>"Address[$i][id]",'value'=>$address->id));
    }
?>
        <table>
            <tr>
                <td>Тип</td>
                <td>
                    <div class="addr-type">
                        <input id="type0<?=$i?>" type="radio" name="Address[<?=$i?>][type]" value="0" <?=$address->type==0?'checked="true"':''?> />
                        <label for="type0<?=$i?>">Магазин</label>
                        <input id="type1<?=$i?>" type="radio" name="Address[<?=$i?>][type]" value="1" <?=$address->type==1?'checked="true"':''?> />
                        <label for="type1<?=$i?>">Сервис-центр</label>
                    </div>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Город</td>
                <td>
                    <select name="Address[<?=$i?>][city]">
                        <?foreach ($cities as $city):?>
                        <option <?if($city['id'] == $address->city):?>selected="selected"<?endif;?> value="<?=$city['id']?>"><?=$city['title']?></option>
                        <?endforeach;?>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Телефоны</td>
                <td>
                    <div class="phones-<?=$i?>"></div>
                    <button type="button" onclick="addmorephone('','<?=$i?>')">+Добавить еще</button>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Факс</td>
                <td><input type="text" name="Address[<?=$i?>][fax]" value="<?=$address->fax?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Skype</td>
                <td><input type="text" name="Address[<?=$i?>][skype]" value="<?=$address->skype?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>ICQ</td>
                <td><input type="text" name="Address[<?=$i?>][icq]" value="<?=$address->icq?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>E-mail</td>
                <td><input type="text" name="Address[<?=$i?>][email]" value="<?=$address->email?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Адрес</td>
                <td><input type="text" name="Address[<?=$i?>][address]" value="<?=$address->address?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Доставка</td>
                <td><input type="text" name="Address[<?=$i?>][delivery]" value="<?=$address->delivery?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <?/*<tr>
                <td>Время работы</td>
                <td>
                    <div id="worktime">
                        <div><input type="text" name="Address[<?=$i?>][worktime][times][0]" value="<?=$address->worktime->times[0]?>" /></div>
                        <input type="hidden" name="Address[<?=$i?>][worktime][times][1]" value="-" />
                        <div><input type="checkbox" <?if($address->worktime->days->Monday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Monday]" /> Пн</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Tuesday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Tuesday]" /> Вт</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Wednesday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Wednesday]" /> Ср</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Thursday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Thursday]" /> Чт</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Friday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Friday]" /> Пт</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Saturday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Saturday]" /> Сб</div>
                        <div><input type="checkbox" <?if($address->worktime->days->Sunday==0):?>checked="checked"<?endif;?> value="0" name="Address[<?=$i?>][worktime][days][Sunday]" /> Вс</div>
                    </div>
                </td>
                <td>&nbsp;</td>
            </tr>*/?>
        </table>
        </div>
        <? endforeach; ?>
        </div>
        <a href="#" class="x3-button" id="add-shop">Добавить магазин</a>
    </fieldset>
<table>
<tr><td colspan="3"><?=X3_Html::form_tag('button',array('%content'=>'Сохранить','type'=>'submit'))?></td></tr>
</table>
</div>
    
<div id="services">
    <?php
        if($subaction == 'edit'){
            $cserv = Company_Service::get(array('company_id'=>$modules->id),1,null,1);
            if($cserv==null){
                $cserv = array('services'=>array(),'groups'=>array());
                
            }else{
                $cserv['services'] = json_decode($cserv['services']);
                $cserv['groups'] = json_decode($cserv['groups']);
            }
        }else
            $cserv = array('services'=>array(),'groups'=>array());
        $services = X3::db()->fetchAll("SELECT * FROM data_service");
        $scnt = ceil(count($services)/4);
        $categories = Shop_Category::get(array(),false,null,true);
        $groups = Shop_Group::get();
        $js_cats = array();
        foreach($categories as $j=>$cat){
            $js_cats[$cat['id']]['title'] = $cat['title'];
            foreach ($groups as $i => $gr) {
                $a = array_map(function($v){return intval($v);},explode(',',$gr->category_id));
                if(in_array($cat['id'],$a)){
                    $js_cats[$cat['id']]['groups'][$gr->id] = $gr->title;
                    $categories[$j]['groups'][$gr->id]=$groups[$i];
                }
            }
        }
        $js_services = array();
    ?>
    <div style="float:left;width:320px;border-right:1px solid #666666;margin-right:10px">
    <div id="service_holder" class="holder">
    <? foreach ($services as $i=>$service): $js_services[$service['id']] = $service['title']?>
    <input style="visibility:hidden;position:absolute;left:-999px" id="service_<?=$service['id']?>"  type="checkbox" <?=($cserv['services']!=null && in_array($service['id'],$cserv['services'])?'checked="checked"':'')?> name="Company_Service[services][]" value="<?=$service['id']?>">
    <?if($cserv['services']!=null && in_array($service['id'],$cserv['services'])):?>
    <div class="service" kid="#service_<?=$service['id']?>">
                <div class="block"><a class="service_remove" href="#" onclick="return false;"><img src="/images/cross.png" /></a></div>
                <div class="cont"><span><?=$service['title']?></span></div>
    </div>
    <?endif;?>
    <? endforeach; ?>
    </div>
    <button type="button" primary-icon="ui-icon-plusthick" id="addservo">Добавить услугу</button>
    </div>
    <div style="margin-left:350px;">
    <div id="groups_holder" class="holder">
    <? foreach ($categories as $i=>$cat):?>
            <? if(!empty($cat['groups'])):?>
            <?foreach ($cat['groups'] as $j=>$service): ?>
                <input style="visibility:hidden;position:absolute;left:-999px" id="groups_<?=$service->id?>"  type="checkbox" <?=($cserv['groups']!=null && in_array($service->id,$cserv['groups'])?'checked="checked"':'')?> name="Company_Service[groups][]" value="<?=$service->id?>">
                <?if($cserv['groups']!=null && in_array($service->id,$cserv['groups'])):?>
                <div class="service" kid="#groups_<?=$service->id?>">
                            <div class="block"><a class="service_remove" href="#" onclick="return false;"><img src="/images/cross.png" /></a></div>
                            <div class="cont"><span><?=$service->title?></span></div>
                </div>
                <?endif;?>                
            <? endforeach; ?>
            <?endif;?>
    <? endforeach; ?>
    </div>
    <button type="button" primary-icon="ui-icon-plusthick" id="addcato">Добавить группу</button>
    </div>
    <br clear="left" />
    <hr/>
    
<table>
    <tr><td colspan="3"><?=X3_Html::form_tag('button',array('%content'=>'Сохранить','type'=>'submit'))?></td></tr>
</table>        
</div>
    
<?=$form->end();?>
<?if(count($bill)>0):?>
<div id="bills">
    <ul id="bill-types">
        <? 
        $k = -1;
        $s=0;
        foreach ($bill as $b): 
        ?>
        <?if($k!=$b['type']):?>
        <?if($k>-1) 
            echo '<p><b>Итого:'.$s.'</b></p></div></li><li><h3>'.Company_Bill::$paytype[$b['type']].'</h3>';
        else
            echo '<li><h3>'.Company_Bill::$paytype[$b['type']].'</h3>';
        echo'<div style="padding:10px;border-bottom:1px solid #cacaca;background:#fafafa">';?>
        <?
        $s=0;
        $k = $b['type'];
        endif;?>
        <p>
            &nbsp;<b><i><?=$b['document']?></i></b><br/>
            <span><?=date('d.m.Y',$b['created_at'])?></span> - <span><?=date('d.m.Y',$b['ends_at'])?></span> оплата <?=$s+=$b['paysum']?>.
        </p>
        <? endforeach; ?>
        <?='<p><b>Итого:'.$s.'</b></p></div></li>'?>
    </ul>
</div>
<?endif;?>
</div>
</div>
<script>    
    var pno = {};
    var ano = 0
    var acnt = <?=$addreses->count();?>;
    var services = <?=  json_encode($js_services)?>;
    var groups = <?=  json_encode($js_cats);?>;
    function addmorephone(val,id){
        if(typeof val == 'undefined') val = '';
        var div = $('<div />').addClass('phone');
        if(typeof pno[id] == 'undefined' || pno[id] === null) pno[id] = 0;
        var ph = $('<input />').attr({'name':'Address['+id+'][phones]['+pno[id]+']'}).val(val);
        div.append(ph);
        if(pno[id]>0){
            div.append($('<img />').attr({'src':'/images/cross.png'}).css({'margin':'-3px 0 0 3px'}).click(function(){
                $(this).parent().remove();
            }))
        }
        $(".phones-"+id).append(div);
        pno[id]++;
    }
    $('.addr-type').buttonset();
    <?if(isset($_POST['Address']) || !$address->table->getIsNewRecord()):
        
        //if(isset($_POST['Address'][0]['phones']))
            //$phones = $_POST['Address'][0]['phones'];
        //else
        foreach($addreses as $i=>$address):
            $phones = $address->phones;
        ?>
        <?foreach($phones as $phone):?>
            addmorephone('<?=  addslashes($phone)?>','<?=$i?>');
        <?  endforeach;?>
        <?  endforeach;?>
    <?else:?>
        addmorephone('','0');
    <?endif;?>
     
     
     /*
      * Main logic
      */
     $(function(){
         $('#tabs').tabs();
         $('.overlay').live('click',function(e){
             $('.shop.active').removeClass('active');
             $(this).parent().addClass('active');
         })
         $('#add-shop').click(function(){
            var tmpl = $('<div />').attr({'class':'shop shop'+acnt});
            tmpl.append('<div class="overlay">&nbsp;</div>');
            var tbl = $('<table />');
            tbl
            .append($('<tr />').append(
                    '<td>Тип</td>'
                ).append(
                    $('<td />').append($('<div class="addr-type">')
                        .append($('<input />').attr({'id':"type0"+acnt, 'type':"radio", 'name':"Address["+acnt+"][type]", 'value':"0",'checked':"true"}))
                        .append($('<label />').attr({'for':"type0"+acnt}).html('Магазин'))
                        .append($('<input />').attr({'id':"type1"+acnt, 'type':"radio", 'name':"Address["+acnt+"][type]", 'value':"1"}))
                        .append($('<label />').attr({'for':"type1"+acnt}).html('Сервис-центр'))
                        .buttonset()
                    )
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Город</td>'
                ).append(
                    $('<td />').append($('<select name="Address['+acnt+'][city]">')
                        .append($('.shop:first td select[name$="[city]"]').html().replace('selected="selected"',''))
                    )
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Телефоны</td>'
                ).append(
                    $('<td />').append('<div class="phones-'+acnt+'"></div>').append($('<button type="button" onclick="addmorephone(\'\',\''+acnt+'\')">+Добавить еще</button>').button())
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Факс</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][fax]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Skype</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][skype]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>ICQ</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][icq]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>E-mail</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][email]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Адрес</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][address]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            .append($('<tr />').append(
                    '<td>Доставка</td>'
                ).append(
                    $('<td />').append('<input type="text" name="Address['+acnt+'][delivery]" value="" />')
                ).append(
                    $('<td>&nbsp;</td>')
                )
            )
            tmpl.append(tbl);
            $('.shop.active').removeClass('active');
            tmpl.css({'left':(20*acnt)+'px'}).addClass('active');
            tmpl.insertAfter($('.shop:last'));
            addmorephone('',acnt);
            acnt++;
            return false;
         })
         $('#addservo').click(function(){
             var isnew = $(this).attr('isnew')==1;
             var sel = null;
             if(!isnew){
                 sel = $('<select />').attr('id','servotitle');
                 for(i in services){
                     if(!$('#service_'+i).is(':checked')){
                         sel.append('<option value="'+i+'">'+services[i]+'</option>');
                     }
                 }
             }
             if(typeof $('#servadd')[0] != 'undefined'){
                 $('#servadd').dialog('destroy');
             }
            $('<div />').attr({'id':'servadd','title':'Добавить услугу'}).append('<b>Название</b>').append(
                isnew?'<input type="text" value="" id="servotitle" />':sel
            )
            .dialog({modal:true,width:400,buttons:{
                    'Добавить':function(){
                        var val = $(this).find('#servotitle').val();
                        var text = $(this).find('option:selected').text();
                        if(isnew){                                
                            if(val.replace(/[\s]+/,'')!=''){
                                $.post('/service/insert',{title:val},function(m){
                                    var ii = parseInt(m);
                                    if(isNaN(ii))
                                        $.growl('Ошибка сохранения!');
                                    else{
                                        $('#service_holder').prepend('<input style="visibility:hidden;position:absolute;left:-999px" id="service_'+ii+'" name="Company_Service[services][]" checked="checked" value="'+ii+'">');
                                        $('#service_holder').append($('<div />').attr({'kid':'#service_'+ii,'class':'service'})
                                            .append('<div class="block"><a href="#" class="service_remove"><img src="/images/cross.png" /></a></div>')
                                            .append('<div class="cont"><span>'+val+'</span></div>')
                                        )
                                    }
                                }).error(function(){$.growl('Ошибка сохранения!');})
                                $(this).dialog('close');
                            }else
                                $.growl('Не заполнено имя!')
                        }else{
                            $("#service_"+val).attr('checked',true);
                            $('#service_holder').append($('<div />').attr({'kid':'#service_'+val,'class':'service'})
                                .append('<div class="block"><a href="#" class="service_remove"><img src="/images/cross.png" /></a></div>')
                                .append('<div class="cont"><span>'+text+'</span></div>')
                            )
                            $(this).dialog('close');
                        }
                    },
                    'Отменить':function(){
                        $(this).dialog('close');
                    }
            }})
         })
         $('#addcato').click(function(){
            sel = $('<select />').attr('id','groupstitle');
            for(i in groups){
                var opt = $('<optgroup />').attr('label',groups[i].title);
                for(j in groups[i].groups)
                if(!$('#groups_'+j).is(':checked')){
                    opt.append('<option value="'+j+'">'+groups[i].groups[j]+'</option>');
                }
                sel.append(opt);
            }
            if(typeof $('#grpadd')[0] != 'undefined'){
                $('#grpadd').dialog('destroy');
            }
            $('<div />').attr({'id':'grpadd','title':'Добавить группу'}).append('<b>Название</b>').append(sel)
            .dialog({modal:true,width:400,buttons:{
                'Добавить':function(){
                    var val = $(this).find('#groupstitle').val();
                    var text = $(this).find('option:selected').text();

                    $("#groups_"+val).attr('checked',true);
                    $('#groups_holder').append($('<div />').attr({'kid':'#groups_'+val,'class':'service'})
                        .append('<div class="block"><a href="#" class="service_remove"><img src="/images/cross.png" /></a></div>')
                        .append('<div class="cont"><span>'+text+'</span></div>')
                    )
                    $(this).dialog('close');
                },
                'Отменить':function(){
                    $(this).dialog('close');
                }
        }})            
         })
        $('a.service_remove').live('click',function(){
            var val = $(this).parent().parent().attr('kid');
            $(val).attr('checked',false);
            $(this).parent().parent().remove();
            return false;
        })
        
        $('#bill-types li').each(function(){
            $(this).css({'list-style-type':'none'}).find('h3').addClass('ui-widget-header').css({'cursor':'pointer','margin-bottom':'0'}).click(function(){
                $(this).siblings('div').slideToggle();
            })
            $(this).children('div').addClass('ui-widget-content').css('display','none');
        });
     })
</script>
<?endif;?>
</div>