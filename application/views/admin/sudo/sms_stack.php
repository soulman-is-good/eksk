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
                <a class="button inline-block" id="clearstack" href="/sms/clearstack">Очистить стек</a>
                <a class="button inline-block" id="import" href="/uploads/excel/generate/sms.xls">Экспорт в Excel</a>
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
                            <a href="/admin/view/module/<?=$class?>/id/<?=$model[$pk]?>.html"><?=  $model['phone']?></a><br/>
                            <p style="font:11px Tahoma;color:#888"><?=$model['text']?></p>
                            <em style="font-size:10px"><b>Добавлено:</b><?=date('d.m.Y H:i:s',$model['created_at'])?></em>
                            <?if($model['status']>-1):?>
                            <br/><em style="font-size:10px"><b>Обработано:</b><?=date('d.m.Y H:i:s',$model['sent_at'])?></em>
                            <?endif;?>
                        </div>
                        <div class="right_side" style="float:right;position:relative;top:-8px;text-align: right;width:250px;">
                            <?/*<a href="/admin/edit/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-rb no-rt">Редактировать</a>*/?>
                            <?if($model['status']>0):?>
                            <br/><em title="code#<?=$model['status']?>" style="color:#<?=dechex($model['status']%16).dechex($model['status']%8)?>A"><?=Sms::$errorCodes[(int)$model['status']]?></em><br/><br/>
                            <div class="wrapper"><a href="/sms/resend/id/<?=$model[$pk]?>.html" class="button no-lt no-lb">Переотправить</a></div>
                            <?elseif($model['status']==0):?>
                            <br/><em style="color:#5A5"><?=Sms::$errorCodes[$model['status']]?></em><br/><br/>
                            <?else:?>
                            <br/><em style="color:#D55">В очереди</em><br/><br/>
                            <?endif;?>
                            <div class="wrapper"><a href="/admin/delete/module/<?=$class?>/id/<?=$model[$pk]?>.html" class="button no-lt no-lb">Удалить</a></div>
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