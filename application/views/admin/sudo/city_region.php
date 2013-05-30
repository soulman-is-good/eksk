<?php
$class = ucfirst($_GET['module']);
$module = X3_Module::getInstance($class);
$scope = $module->getDefaultScope();
//$count = X3_Module_Table::num_rows($scope,$class);
//$paginator = new Paginator($class."-Admin", $count,null,false);
//$scope['@limit'] = $paginator->limit;
//$scope['@offset'] = $paginator->offset;
$cities = City::get(array());
$title = $module->moduleTitle();
$pk = $module->getTable()->getPK();
$sel = X3_String::create($cities[0]->title)->translit();
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block">
                <a class="button inline-block" id="import" href="#import">Импорт улиц из Excel</a>
                <a class="button inline-block" id="send_message" href="/admin/create/module/<?=$class?>.html">Добавить</a>
            </div>
        </div>
        <h1><?=$title?></h1>
    </div>
    <div class="content">
        <div class="admin-list">
            <div class="tabs" id="tabs" fctabs="<?=$sel?>">
                <ul>
                    <?foreach($cities as $city):?>
                    <li><a cid="<?=$city->id?>" href="#<?=X3_String::create($city->title)->translit()?>"><?=$city->title?></a></li>
                    <?endforeach;?>
                </ul>
            <?foreach($cities as $city):
                $models = X3_Module_Table::get(array('@condition'=>array('city_id'=>$city->id),'@order'=>'weight'),0,$class,1);    
            ?>
            <div class="tab" id="<?=$cityname=X3_String::create($city->title)->translit()?>">
            <?foreach($models as $model):?>
                <div class="message_block" style="height:auto;min-height: 0">
                    <div class="inside_block">
                        <div class="middle_side" style="width:auto">
                                <a href="/admin/view/module/<?=$class?>/id/<?=$model[$pk]?>.html"><?=isset($model['title'])?$model['title']:(isset($model['name'])?$model['name']:$model[$pk])?></a>
                        </div>
                        <div class="right_side" style="float:right;position:relative;top:-8px;text-align: right;width:250px;">
                            <div class="wrapper"><a href="/admin/edit/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-rb no-rt">Редактировать</a>
                            <a href="/admin/delete/module/<?=$class?>/id/<?=$model[$pk]?>.html?hash=<?=$cityname?>" class="button no-lt no-lb">Удалить</a></div>
                        </div>
                    </div>
                </div>
            <?endforeach;?>
            </div>
            <?endforeach;?>
            </div>
        </div>
    </div>
    <div id="navi">
            <?//$paginator?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<div id="form_tmpl" style="display:none">
    <div>
    <form class="test-form" method="post" action="/city/import" enctype="multipart/form-data" target="for_files">
        <div class="errors" style="display:none;background: #B8ED67"></div>
        <table class="eksk-form" width="100%">
            <tr>
                <td class="label" width="70">
                    <label for="excel">Файл Excel</label>
                    <input type="hidden" name="city_id" value="" class="city_id" />
                </td>
                <td class="field" style="padding:5px 0px">
                    <input type="file" class="file-xls" name="excel" />
                </td>
            </tr>
            <tr>
                <td class="label">&nbsp;</td>
                <td class="field">
                    <div class="table_faq">
                        <div class="bg_form"><button type="submit"><?=X3::translate('Отправить')?></button></div>

                        <div class="clear">&nbsp;</div>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    </div>
</div>
<iframe style="position:absolute;left:-9999px;visibility: hidden;width:0px;height:0px" name="for_files" id="for_files"></iframe>
<script>
    var eform = null;
    var dia;
    $(function(){
        $('#import').click(function(){
            eform = $($('#form_tmpl').html());
            dia = $.dialog(eform,'Импорт улиц','no buttons').setSize(750).setRelativePosition('center');
            eform.find('.test-form').submit(function(){
                eform.find('.city_id').val($('#tabs ul a.active').attr('cid'));
                $.loader();
            })
            return false;
        });
        $('#for_files').load(function(){
            var html = $(this).contents().find('body').html();
            if(typeof $.rusWindows['@loader'] !== 'undefined')
                    $.loader();
            if(html!='' && html != null){
                var json = eval('(' + html + ')');
                if(json.status == 'OK'){
                    eform.find('.errors').html(json.message).css('display','block');
                    eform.find('.eksk-form').css('display','none');
                    dia.onclose = function(){location.reload();}
                }else{
                    $.alert(json.message,'Ошибка!');
                }
            }
        })
    })
</script>