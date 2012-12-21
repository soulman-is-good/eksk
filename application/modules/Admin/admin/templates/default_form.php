<style>
    .x3-errors{
        color:#FF0000;
    }
</style>
<?/*STANDART TEMPLATE*/
if($modules instanceOf X3_Module_Table):
$form = new Form($modules);
if($_GET['add']!='' && isset($modules->table['parent_id'])){
    $modules->parent_id = $_GET['add'];
}
?>
<div id="x3-buttons" x3-layout="buttons"></div>
<div id="x3-header" x3-layout="header"><a href="/admin/<?=$action?>"><?=$moduleTitle;?></a> > <?if($subaction=='add'):?>Добавить<?else:?>Редактировать<?endif;?></div>
<div class="x3-paginator" x3-layout="paginator"></div>
<div class="x3-functional" x3-layout="functional"></div>
<div class="x3-main-content" style="clear:left">
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
<?=$form->start(array('action'=>"/admin/$action/save"));?>
<?=$form->render();?>
<?=$form->end();?>
</div>
<?endif;?>