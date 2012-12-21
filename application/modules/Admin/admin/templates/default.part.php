<?/*STANDART TEMPLATE*/
if($modules instanceOf X3_Module_Table):
$info = $modules->_fields;
$pk = $modules->getTable()->getPK();
if(!isset($labels))
    $labels = $modules->fieldNames();
$class = get_class($modules);
$action = array_search($class,$this->aliases);
$actions = $this->modules[X3::app()->user->group][$class];
$tree = false;
$path = "@views:admin:templates:$action.part.php";
if(!is_file(X3::app()->getPathFromAlias($path)))
    $path = "@views:admin:templates:default.part.php";
?>
<table width="100%">
<tr>
        <th><input type="checkbox" id="check-deleteall" onclick="$('[id^=\'delete-\']').each(function(){$(this).attr('checked',$('#check-deleteall').is(':checked'))})" /></th>
<?foreach ($labels as $name=>$value):
    if(isset($info['ref']) && isset($info['tree'])){
        $tree = array('name'=>$name,'query'=>$info['tree']);
    }
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
foreach ($modules as $module):?>
    <tr rowid="<?=isset($pk)?$module->$pk:0?>">
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
<?if($tree !== false):
    $submodels = $info[$tree['name']]['ref'][0];
    $sattr = $info[$tree['name']]['ref'][1];
    $query = $tree['query'];
    $query['@condition'][$sattr] = $module->{$tree['name']};
    $submodels = X3_Module_Table::get($query, false, $submodels);
    echo '<tr><td colspan="'.(count($labels) + count($actions) + 1).'">' . $this->renderPartial($path,array('modules'=>$submodels)) . '</td></tr>';
    ?>
    
<?endif;?>
<?endforeach;?>
</table>
<?endif;?>