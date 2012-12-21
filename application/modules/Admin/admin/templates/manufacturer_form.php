<?php
if($modules->getTable()->getIsNewRecord()){
$grps = X3::db()->query("SELECT DISTINCT sg.* FROM shop_group sg INNER JOIN shop_item si ON si.group_id=sg.id WHERE sg.status AND si.manufacturer_id='$modules->id' ORDER BY sg.title, sg.weight");
$i = 0;
$groups = array();
$text = array();
while($g=mysql_fetch_array($grps)){
    $pps = X3::db()->query("SELECT id, group_id, title, value, property_id FROM shop_proplist WHERE group_id={$g['id']} AND property_id IN (SELECT id FROM shop_properties WHERE isgroup AND group_id={$g['id']}) ORDER BY weight, title");
    if(is_resource($pps) && mysql_num_rows($pps)>0){
        while ($ps = mysql_fetch_assoc($pps)){
            $groups[]=array(
                'title'=>$ps['title'],
                'gid'=>$g['id'],
                'pid'=>$ps['id'],
            );
            $key = $modules->id . '|' . $g['id'] . '|' . $ps['id'];
            if(NULL!==($t = Manufacturer_Group::get(array('manufacturer_id'=>$modules->id,'gid'=>$g['id'],'pid'=>$ps['id']),1))){
                $text[$key] = $t->text;
            }else
                $text[$key] = '';
        }
    }else{
        $groups[]=array(
            'title'=>$g['title'],
            'gid'=>$g['id'],
            'pid'=>0
        );
        $key = $modules->id . '|' . $g['id'] . '|0';
        if(NULL!==($t = Manufacturer_Group::get(array('manufacturer_id'=>$modules->id,'gid'=>$g['id'],'pid'=>'0'),1))){
            $text[$key] = $t->text;
        }else
            $text[$key] = '';
    }
    $i++;
}
}
?>
<style>
    .x3-errors{
        color:#FF0000;
    }
    .x3-main-content .html {
        width:400px;
        height:300px;
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
<div id="mtabs">
    <ul>
        <li><a href="#common">Основные</a></li>
        <?if($modules->getTable()->getIsNewRecord()):?>
        <li><a href="#text">Текстовка</a></li>
        <?endif;?>
    </ul>
    <div id="common">
<?
$errs = $modules->getTable()->getErrors();
if(!empty($errs)):?>
    <div class="x3-errors">
        <ul>
            <?foreach($errs as $err)
                foreach($err as $er):?>
            <li><?=$er?></li>
            <?endforeach;?>
            <!--li><?=X3::db()->getErrors()?></li-->
        </ul>
    </div>
<?endif;?>    
<?=$form->start(array('action'=>"/admin/$action/save"));?>
<?=$form->render();?>
<?=$form->end();?>
    </div>
<?if($modules->getTable()->getIsNewRecord()):?>
    <div id="text">
        <form action="/manufacturer_Group/update" method="post" enctype="multipart/form-data">
        <ul>
            <? foreach ($groups as $g): $key = $modules->id . '|' . $g['gid'] . '|' . $g['pid']?>
            <li>
                <div style="background: #dadada;padding:5px;font-size:14px;font-weight:bold;cursor:pointer;" onclick="$(this).siblings('.slide').slideToggle();"><?=$g['title']?></div>
                <div class="slide" style="display:none">
                    <input name="Manufacturer_Group[<?=$modules->id?>][<?=$g['gid']?>|<?=$g['pid']?>][pid]" type="hidden" value="<?=$g['pid']?>" />
                    <textarea id="<?=md5($key)?>" name="Manufacturer_Group[<?=$modules->id?>][<?=$g['gid']?>|<?=$g['pid']?>][text]" style="width:100%;height:200px"><?=$text[$key]?></textarea>
                    <script>
                        CKEDITOR.replace( '<?=md5($key)?>' );
                    </script>                    
                </div>
            </li>
            <? endforeach; ?>
        </ul>
        <button type="submit">Сохранить</button>
        </form>
    </div>
<?endif;?>
</div>
</div>
        <script type="text/javascript">
        $('#mtabs').tabs();
    </script>
<?endif;?>