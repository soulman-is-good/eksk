<?php
$errors = $user->getTable()->getErrors();
$addd = X3::translate('Добавление жильца');
$fields = array('name','surname','email','phone','password');
if($user->role == 'ksk'){
    $fields = array('name'=>X3::translate('Название КСК'),'email'=>"E-mail",'phone'=>X3::translate('Телефон') . ' +7','password'=>X3::translate('Пароль'));
    $addd = X3::translate('Добавление КСК');
    $aform = new Form($address);
}
$form = new Form($user);
?>
<div class="eksk-wnd<?=(!X3::user()->isGuest()?'':' login')?>">
    <div class="head"><h1<?=(!X3::user()->isGuest()?'':' class="center"')?>><?=$addd?></h1></div>
    <div class="content">
        <?if(!empty($errors)):?>
        <div class="errors">
            <ul>
                <?foreach($errors as $errs):?>
                    <?foreach($errs as $err):?>
                <li><?=$err?></li>
                    <?endforeach;?>
                <?endforeach;?>
            </ul>
        </div>
        <?endif;?>
        <?=$form->start()?>
        <table class="eksk-form">
        <?
        echo $form->renderPartial($fields);
        ?>
            <?if($user->role == 'ksk'):?>
            <tr><td colspan="3"><div class="hr" style="margin:10px 0;">&nbsp;</div></td></tr>
            <tr><td class="label">&nbsp;</td><td class="field" colspan="2"><h3><?=X3::translate('Адрес офиса');?></h3></td></tr>
            <tr>
                <td class="label">
                    <label><?=$address->fieldName('city_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=$aform->select('city_id',array('class'=>'city_id','fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?=$aform->error('city_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$address->fieldName('region_id')?></label>
                </td>
                <td class="field">
                    <div class="wrapper inline-block"><?=X3_Html::form_tag('select',array('class'=>'region_id','id'=>'User_Address_region_id','name'=>'User_Address[region_id]','rid'=>$address->region_id,'fcselect'=>'1','data-width'=>'345'))?></div>
                </td>
                <td class="error">
                    <?=$aform->error('region_id')?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label><?=$address->fieldName('house')?></label>
                </td>
                <td class="field">
                    <div class="wrapperEx inline-block">
                        <input style="width:47px;" type="text" name="User_Address[house]" id="Forum_house" value="<?=addslashes($address->house)?>" />
                        <?/*<select fcselect data-width="345" name="Forum[house]" id="Forum_house" hid="<?=addslashes($address->house)?>"></select>*/?>
                    </div>
                        <label><?=$address->fieldName('flat')?></label>
                        <div class="wrapper inline-block"><?=$aform->input('flat',array('style'=>"width:47px"))?></div>
                </td>
                <td class="error">
                    <div><?=$aform->error('house')?></div>
                    <?=$aform->error('flat')?>
                </td>
            </tr>
            <tr>
                <td><input type="hidden" id="coord" name="User_Address[coord]" value="<?=$address->coord?>" /></td>
                <td colspan="2">
                    <a class="map_link inline-block mb-10 map-link" href="#coord"><span><?=X3::translate('Указать на карте');?></span><span style="display:none"><?=X3::translate('Спрятать карту');?></span></a>
                    <div class="map" style="display:none">
                        <div></div>
                    </div>
                </td>
            </tr>            
            <?endif;?>
            <tr><td>&nbsp;</td><td align="left" colspan="2"><div class="wrapper inline-block"><button type="submit"><?=X3::translate('Добавить');?></button></div></td></tr>
        </table>
        <?=$form->end()?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/javascript">
    $(function(){
        function phone(el,o,t){
            var val = el.val().split(' ');
            var code = val.shift();
            var phone = val.join(' ');
            var in_code = $('<input />').data('elem',el).css({'width':'36px'}).change(function(){
                $(this).data('elem').updateVal();
            }).val(code).attr({'type':'text','maxlength':o}).addClass('string').mask("9".repeat(o))
            .insertBefore(el).wrap($('<div class="wrapper inline-block" style="margin-right:5px"></div>'));
            var in_phone = $('<input />').data('elem',el).css({'width':'288px'}).change(function(){
                $(this).data('elem').updateVal();
            }).val(phone).attr({'type':'text','maxlength':t}).addClass('string').mask("999 99 99")
            .insertBefore(el).wrap($('<div class="wrapper inline-block"></div>'));
            el.data({'code':in_code,'phone':in_phone}).updateVal = function(){
                var a = in_code.val();
                var b = in_phone.val();
                if(a=='' && b=='')
                    $(this).val('');
                else
                    $(this).val(a+' '+b);
            }
            el.css({opacity:0,width:0,height:0,position:'absolute','left':'-9999px'}).attr({'tabindex':'-1'}).parent().removeClass('wrapperEx');
        }
        phone($('#User_phone'),3,9);
        
        
        //Address
        $('.city_id').live('change',function(){
            var city_id = $(this).val();
            var C = this;
            $.get('/city/region.html',{id:city_id},function(m){
                var R = $('#User_Address_region_id');
                var rid = R.attr('rid');
                R.html('');
                for(i in m){
                    var o = $('<option />').attr({'value':m[i].id}).data('houses',m[i].houses).html(m[i].title);
                    if(m[i].id == rid)
                        o.attr('selected',true);
                    $('#User_Address_region_id').append(o);
                }
                R.data('fcselect').redraw()
                //$(C).parent().parent().parent().parent().find('.region_id').change();
            },'json')
        })
        //$('.region_id').live('change',function(){
            //var H = $('#Forum_house');
            //var house = H.attr('hid');
            //H.html('');
            //var m = $(this).children(':selected').data('houses');
            //H.data('fcselect').redraw();
        //});
        $('.city_id').change();
        
        //Init maps
        $('.map-link').live('click',function(){
            var href = $(this).attr('href');
            var coords = $(href).val().split('|');
            this.type = 'yandex#map';
            this.zoom = 16;
            var self = this;
            var nocoords = false;
            if(coords.length < 3 && typeof ymaps != 'undefined'){
                coords = [ymaps.geolocation.longitude,ymaps.geolocation.latitude];
                nocoords = true;
            }else if(coords.length < 3) {
                coords = [76.943776,43.295904];
                nocoords = true;
            }else{
                if(coords.length == 4)
                    this.zoom = coords.pop();
                this.type = coords.pop();
            }
            $(this).siblings('.map').slideToggle(function(){
                if(typeof $(this).data('map') == 'undefined'){
                    var map = new ymaps.Map(this,{center:coords,zoom:self.zoom,type:self.type});
                    var zc = new ymaps.control.ZoomControl();
                    var ts = new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]);
                    map.controls.add(zc).add(ts);
                    zc.events.add('zoomchange',function(e){
                        self.zoom = e.get('newZoom');
                        setCoords(coords);
                    })
                    map.events.add('typechange',function(e){
                        self.type=e.get('newType');
                        setCoords(coords);
                    })
                    //var placemark = new ymaps.Placemark(coords,{iconContent: '<?=addslashes($user->fullname)?>'},{preset: 'twirl#greenStretchyIcon'});
                    var placemark = new ymaps.Placemark(coords,{},{preset: 'twirl#greenStretchyIcon',draggable:true});
                    //On placemark dragging
                    placemark.events.add('dragend', function(e) {
                        coords = e.get('target').geometry.getCoordinates();
                        setCoords(coords);
                    });
                    map.geoObjects.add(placemark);
                    $('#User_Address_city_id, #User_Address_region_id, #Forum_house').change(function(){
                        var text = '';
                        text += 'г. ' + $('#User_Address_city_id').children(':selected').text();
                        if($('#Forum_house').val() != '')
                            text += ', ' + $('#Forum_house').val();
                        text += ', ' + $('#User_Address_region_id').children(':selected').text();
                        console.log(text)
                        ymaps.geocode(text).then(function(res){
                            coords = res.geoObjects.get(0).geometry.getCoordinates();
                            setCoords(coords);
                            map.panTo(coords,{duration:2000});
                            placemark.geometry.setCoordinates(coords);
                        },function(err){})
                    })
                    if(nocoords)
                        $('#Forum_house').change();
                    $(this).data({'map':map,'placemark':placemark});
                }else{
                    var map = $(this).data('map');
                    
                }
            });
            $(this).children('span').toggle();
            return false;
        })        
    });
    function setCoords(coords){
        if(typeof coords.join != 'function')
            return false;
        var val = coords.join('|');
        var a = $('[href="#coord"]');
        val += '|' + a[0].type;
        val += '|' + a[0].zoom
        $('#coord').val(val)
        return true;
    }    
</script>