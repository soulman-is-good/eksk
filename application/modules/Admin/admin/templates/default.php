<?/*STANDART TEMPLATE*/
if($modules instanceOf X3_Module_Table):
$info = $modules->_fields;
$pk = $modules->table->getPK();
$metatitle = X3::db()->fetch("SELECT * FROM sys_settings WHERE name='SeoTitle$class'");
$metakeywords = X3::db()->fetch("SELECT * FROM sys_settings WHERE name='SeoKeywords$class'");
$metadescription = X3::db()->fetch("SELECT * FROM sys_settings WHERE name='SeoDescription$class'");
?>
<div class="x3-main-content">
<div id="x3-buttons" x3-layout="buttons">
    <?if(!empty($actions['general'])):?>
    <?foreach ($actions['general'] as $a):?>
    <a id="<?=$a?>" href="/admin/<?=$action?>/<?=$a?>"><?=$actions['labels'][$a]?></a>
    <?endforeach;?>
    <?endif;?>
</div>
<div id="x3-header" x3-layout="header"><?=$moduleTitle;?></div>
<div class="x3-functional" x3-layout="functional"><?=$functional?></div>

<div id="x3-container">
<ul>
    <li><a href="#list">Список</a></li>
    <?if($metatitle!=null):?>
    <li><a href="#seo">SEO</a></li>
    <?endif?>
</ul>
<div id="list">
<table>
<tr>
        <th><input type="checkbox" id="check-deleteall" onclick="$('[id^=\'delete-\']').each(function(){$(this).attr('checked',$('#check-deleteall').is(':checked'))})" /></th>
<?foreach ($labels as $name=>$value):
    if(is_array($value)){
        $wrappers[$name] = $value['@wrapper'];
        $value = $value['@value'];
    }else{
        if(strpos($name,'image')===0){
            $wrappers[$name] = '<img height="64" src="/uploads/'.$class.'/{@'.$name.'}" />';
        }elseif($info[$name][0]=='boolean'){
            $wrappers[$name] = "<span x3-status=\"$name:{@$pk}:$action\">{@$name}</span>";
        }elseif($info[$name][0]=='datetime'){
            $wrappers[$name] = "{{=date('d.m.Y H:i:s',{@$name});}}";            
        }else
            $wrappers[$name] = "{@$name}";
    }
    if(in_array('orderable',$info[$name])):?>
        <th x3-order="<?=$class?>:<?=$name?>" class="col-<?=$name?>"><a href="/admin/<?=$action?>/order/<?=$name."@".(X3::user()->{$action.'-sort'}[1]=='DESC'?'ASC':'DESC')?>"><?=$value?></a></th>
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
        <td><input type="checkbox" id="delete-<?=$module->id?>" value="<?=$module->id?>" name="Delete[<?=$class?>][]" /></td>
<?foreach ($labels as $name=>$value):
    $v = '';
    $matches = array();
    $v = $wrappers[$name];
    if(preg_match_all("/\{@([^\}]+?)\}/", $wrappers[$name],$matches)>0){
        foreach ($matches[1] as $match) {
            $a = $match;
            $v = str_replace("{@$a}", $module->$a, $v);
            if(strpos($module->$a,'http://')===0 && strpos($name,'image')===0){
                $v = str_replace("/uploads/$class/", "", $v);
            }
        }
        
    }
    if(preg_match_all("/\{\{([^\}]+?)\}\}/", $v,$matches)>0){
        foreach ($matches[1] as $k => $match) {
            $a = '';
            eval('$a' . $match . ';');
            $v = str_replace("{$matches[0][$k]}", $a, $v);
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
<div class="x3-paginator" x3-layout="paginator"><?=$paginator?></div>
</div>
<?if($metatitle!=null):?>
    <div id="seo">
        
    </div>
<?endif;?>
</div>
</div>
<?endif;?>