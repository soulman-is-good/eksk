<?/*STANDART TEMPLATE*/
if($modules instanceOf X3_Module_Table):
$info = $modules->_fields;
?>
<div class="x3-main-content">
    
<div id="x3-buttons" x3-layout="buttons">
    <?if(!empty($actions['general'])):?>
    <?foreach ($actions['general'] as $a):?>
    <a href="/admin/<?=$action?>/<?=$a?>">+<?=$moduleTitle?></a>
    <?endforeach;?>
    <?endif;?>
</div>
<div id="x3-header" x3-layout="header"><?=$moduleTitle;?></div>
<div class="x3-paginator" x3-layout="paginator"><?=$paginator?></div>
<div class="x3-functional" x3-layout="functional"><?=$functional?></div>

<div id="x3-container">
<table>
<tr>    
<?foreach ($labels as $name=>$value):
    if(is_array($value)){
        $wrappers[$name] = $value['@wrapper'];
        $value = $value['@value'];
    }else{
        if(strpos($name,'image')===0)
            $wrappers[$name] = '<img height="64" src="/uploads/'.$class.'/{@'.$name.'}" />';
        else
            $wrappers[$name] = "{@$name}";
    }
    if(in_array('orderable',$info[$name])):?>
        <th x3-order="<?=$class?>:<?=$name?>" class="col-<?=$name?>"><a href="/admin/<?=$action?>/order/<?=$name."@".(X3::user()->sort[$action]=='DESC'?'ASC':'DESC')?>"><?=$value?></a></th>
    <?else:?>
        <th class="col-<?=$name?>"><?=$value?></th>
    <?endif?>
<?endforeach;?>
        <th colspan="<?=sizeof($actions['common'])?>"></th>
</tr>
<?
$pk = $modules->table->getPK();
foreach ($modules as $module):?>
    <tr>
<?foreach ($labels as $name=>$value):
    $v = '';
    $matches = array();
    if(preg_match_all("/\{@([^\}]+?)\}/", $wrappers[$name],$matches)>0){
        $v = $wrappers[$name];
        foreach ($matches[1] as $match) {
            $a = $match;
            $v = str_replace("{@$a}", $module->$a, $v);
        }
    }
    ?>
        <td class="row-<?=$name?>">
            <?=$v?>
        </td>    
<?endforeach;?>
<?foreach ($actions['common'] as $name):?>
        <td class="action-<?=$name?>">
            <a href="/admin/<?=$action?>/<?=$name?>/<?=$module->$pk?>"><img src="/application/modules/Admin/images/buttons/<?=$name?>.png" /></a>
        </td>
<?endforeach;?>
    </tr>    
<?endforeach;?>
</table>
</div>
</div>
<?endif;?>