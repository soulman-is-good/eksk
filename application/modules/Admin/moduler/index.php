<?php
$dataTypes = array(
    'string'=>'Строка',
    'integer'=>'Целое число',
    'decimal'=>'Вещественное число',
    'content'=>'Простой текст',
    'text'=>'HTML редактор',
    'email'=>'Эл. почта',
);
?>
<?=X3_Html::open_tag('form', array('id'=>'Moduler_form','name'=>'Moduler_form','method'=>'post'))?>
<div id="divModule_title">
    <div style="display: inline-block;width:128px;text-align: right;padding:2px 5px"><?=  X3_Html::form_tag('label', array('for'=>'Module[name]','%content'=>'Загловок модуля'));?></div>
    <div style="display: inline-block"><?=  X3_Html::form_tag('input', array('name'=>'Module[title]','id'=>'Module_title','value'=>'','type'=>'text'));?></div>
</div>
<div id="divModule_name">
    <div style="display: inline-block;width:128px;text-align: right;padding:2px 5px"><?=  X3_Html::form_tag('label', array('for'=>'Module[name]','%content'=>'Название модуля'));?></div>
    <div style="display: inline-block"><?=  X3_Html::form_tag('input', array('name'=>'Module[name]','id'=>'Module_name','value'=>'','type'=>'text'));?></div>
</div>
<fieldset>
    <legend>Пути к страницам модуля</legend>
<div id="divModule_route_list">
    <div style="display: inline-block;width:128px;text-align: right;padding:2px 5px"><?=  X3_Html::form_tag('label', array('for'=>'Module[route_list]','%content'=>'URL списка'));?></div>
    <div style="display: inline-block"><?=  X3_Html::form_tag('input', array('name'=>'Module[route_list]','id'=>'Module_route_list','value'=>'','type'=>'text'));?></div>
</div>
<div id="divModule_route_text">
    <div style="display: inline-block;width:128px;text-align: right;padding:2px 5px"><?=  X3_Html::form_tag('label', array('for'=>'Module[route_text]','%content'=>'URL внутренней'));?></div>
    <div style="display: inline-block"><?=  X3_Html::form_tag('input', array('name'=>'Module[route_text]','id'=>'Module_route_text','value'=>'','type'=>'text'));?></div>
</div>
</fieldset>
<?=X3_Html::close_tag('form');?>