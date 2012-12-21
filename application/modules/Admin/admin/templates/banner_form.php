<style>
    table.formtable td{
        text-align: left;
    }
</style>
<?php
$form = new Form($modules);
?>
    <div class="x3-main-content" style="">
        <div id="x3-buttons" x3-layout="buttons"></div>
        <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>">Баннер</a> > <? if ($subaction == 'add'): ?>Добавить
            <? else: ?>
            Редактировать<? echo $modules->title; endif; ?>
        </div>
        <div class="x3-paginator" x3-layout="paginator"></div>
        <div class="x3-functional" x3-layout="functional"></div>
        <div id="x3-container"> 
            <div style="padding:10px;clear:both;">                
        <?= $form->start(array('action'=>'/admin/banner/save')); ?>
        <?if($subaction=='edit'):?>
                <input name="Banner[id]" type="hidden" value="<?=$modules->id?>" />
        <?endif;?>                                
        <table>
            <tr>
                <td>Тип баннера</td>
                <td>
                    <select name="Banner[type]" id="Banner_type">
                        <? foreach ($modules->types as $k=>$v): ?>
                        <option value="<?=$k?>"><?=$v?></option>
                        <? endforeach; ?>
                    </select>
                </td>
                <td>*</td>
            </tr>
        <?=$form->renderPartial(array('url','title','file','code','starts_at','ends_at','status'))?>
            <tr>
                <td colspan="3"><button type="submit">Сохранить</button></td>
            </tr>
        </table>
        <?= $form->end();?>
            </div>
        </div>
    </div>