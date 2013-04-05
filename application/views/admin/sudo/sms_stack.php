<?php
$class = ucfirst($_GET['module']);
$module = X3_Module::getInstance($class);
$scope = $module->getDefaultScope();
$count = X3_Module_Table::num_rows($scope,$class);
$paginator = new Paginator($class."-Admin", $count,null,false);
$scope['@limit'] = $paginator->limit;
$scope['@offset'] = $paginator->offset;
$models = X3_Module_Table::get($scope,0,$class,1);    
$title = $module->moduleTitle();
$pk = $module->getTable()->getPK();
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block">
                <?/*<a class="button inline-block" id="import" href="#import">Экспорт в Excel</a>*/?>
            </div>
        </div>
        <h1><?=$title?></h1>
    </div>
    <div class="content">
        <div class="admin-list">
            <?foreach($models as $model):?>
                <div class="message_block" style="height:auto;min-height: 0">
                    <div class="inside_block">
                        <div class="middle_side" style="width:auto">
                            <a href="/admin/view/module/<?=$class?>/id/<?=$model[$pk]?>.html"><?=  $model['phone']?></a>
                        </div>
                        <div class="right_side" style="float:right;position:relative;top:-8px;text-align: right;width:250px;">
                            <div class="wrapper">
                            <?/*<a href="/admin/edit/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-rb no-rt">Редактировать</a>*/?>
                            <?=$model['status']!=-1?'<em style="color:#0F0">Отправлено</em>':'<em style="color:#00F">В очереди</em>'?>
                            <a href="/admin/delete/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-lt no-lb">Удалить</a></div>
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