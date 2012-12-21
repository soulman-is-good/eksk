<style>
    table.formtable td:first-child{
           width:100px;
    }
    table.formtable td{
        text-align: left;
    }
    table.formtable td input[type="text"],table.formtable td select{
        width:400px;
    }
</style>
<?php
$photos = array();
$values = array('value'=>'Значение');
$modules->_fields['value'][0] = $modules->type;
foreach(X3::app()->languages as $lang){
    $l='value_'.$lang;
    $values[$l]='Значение '.  strtoupper($lang);
    $modules->_fields[$l][0] = $modules->type;
}
$form = new Form($modules);
?>
<div class="x3-main-content" style="">
    <div id="x3-buttons" x3-layout="buttons"></div>
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle ?></a> > <? if ($subaction == 'add'): ?>Добавить
        <? else:             ?>
            Редактировать<? endif; ?>
    </div>
    <div class="x3-paginator" x3-layout="paginator"></div>
    <div class="x3-functional" x3-layout="functional"></div>
    <div id="x3-container"> 
        <div style="padding:10px;clear:both;">
            <?= $form->start(array('action'=>'/admin/settings/save')); ?>
            <? if ($subaction == 'edit'): ?>
                <input name="SysSettings[id]" type="hidden" value="<?= $modules->id ?>" />
                <input name="SysSettings[type]" type="hidden" value="<?= $modules->type ?>" />
                <input name="SysSettings[name]" type="hidden" value="<?= $modules->name ?>" />
                <input name="SysSettings[group]" type="hidden" value="<?= $modules->group ?>" />
                <input name="SysSettings[title]" type="hidden" value="<?= $modules->title ?>" />
            <? endif; ?>                
            <table class="formtable">
                <?=
                $form->renderPartial($values)
                ?>
                <tr id="submit">
                    <td colspan="4"><button type="submit">Сохранить</button></td>
                </tr>
            </table>
            <?= $form->end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>