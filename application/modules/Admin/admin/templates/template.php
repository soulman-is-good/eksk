<?/*STANDART TEMPLATE*/?>
<?
/**
 * This script block generates template view for displaying by default
 */
?>
<script id="tmpl-main" type="text/x-jquery-tmpl">
    <tr>
<?foreach ($names as $name=>$value):?>
        <td class="row-<?=$name?>">
            <?=$value?>
        </td>    
<?endforeach;?>
<?foreach ($actions as $name):?>
        <td class="action-<?=$name?>">
            {{html X3['<?=$module?>'].ui['<?=$name?>'].html}}
        </td>
<?endforeach;?>        
    </tr>
</script>
<?
/**
 * Block below is a wraper for #tmpl-main template
 */
?>
<table id="container" width="100%">
<tr>    
<?foreach ($labels as $name=>$value):?>
        <td <?if(in_array('orderable',$info[$name])):?>x3-order="<?=$module?>:<?=$name?>"<?endif?> class="col-<?=$name?>"><?=$value?></td>    
<?endforeach;?>
</tr>
    
</table>