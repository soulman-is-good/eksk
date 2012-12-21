<?php
$items = X3::db()->fetchAll("SELECT si.group_id as group_id, ci.id as cid, si.id as id, si.title as title, si.articule as articule, ci.client_id as client_id, ci.url as url, ci.price FROM company_item ci 
    RIGHT JOIN shop_item si ON si.id=ci.item_id  
    WHERE ci.company_id='$modules->id' ORDER BY si.group_id ASC, si.title ASC");
?>
<style>
    .good{
        padding:10px;
    }
    .good a {
        text-decoration: none;
        border-bottom: 1px dashed #2e6b99;
    }
</style>
<div class="x3-submenu">
    <?=$modules->menu('content');?>
</div>
<div class="x3-main-content">
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle; ?></a> > Товары компании (<?=$modules->title?>)</div>
    <div style="float:right"><a href="/company_Item/deleteall/id/<?=$modules->id?>" onclick="if(!confirm('Вы действительно хотите удалить привязки этой компании?')) return false;">Удалить все</a></div>
    <div id="x3-container">
        <table>
            <tr>
                <th>Наименование</th>
                <th>Цена</th>
                <th>ID клиента</th>
                <th>URL</th>
                <th>&nbsp;</th>
            </tr>
        <?
        $group = "";
        $groups = array();
        if(!empty($items))
        foreach ($items as $item): 
            if(!isset($groups[$item['group_id']])){
                $groups[$item['group_id']] = Shop_Group::getByPk($item['group_id']);
            }
            ?>
            <?if($group != $groups[$item['group_id']]->title):?>
            <tr>
                <td style="font:bold 16px Tahoma;padding:10px" colspan="5"><a href="/admin/shopgroup/edit/<?=$item['group_id']?>"><?=$groups[$item['group_id']]->title?></a></td>
            </tr>
            <?$group = $groups[$item['group_id']]->title;endif;?>
            <tr>
                <td>
                    <div class="good"><a href="/admin/shop/edit/<?=$item['id']?>"><?=$item['title']?><?=$item['articule']!=''?" ({$item['articule']})":''?></a></div>
                </td>
                <td>
                    <input value="<?=$item['price']?>" type="text" x3-update="price:id=<?=$item['cid']?>" />
                </td>
                <td>
                    <input value="<?=$item['client_id']?>" type="text"  x3-update="client_id:id=<?=$item['cid']?>" />
                </td>
                <td>
                    <?=!empty($item['url'])?"<a href='{$item['url']}' target='_blank'>Перейти</a>":''?>
                </td>
                <td>
                    <a href="/company_Item/delete/id/<?=$item['cid']?>"><img src="/application/modules/Admin/images/buttons/delete.png" /></a>
                </td>
            </tr>
        <? endforeach; ?>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('[x3-update]').each(function(){
            $(this).blur(function(){  
                $.post('/company/updateField',{attr:$(this).attr('x3-update'),value:$(this).val()},function(m){
                    if(m=='OK')
                        $.growl('Сохранено!','ok');
                    else
                        $.growl(m);
                }).error(function(){$.growl('Ошибка запроса.')})
            })
        })
    })
</script>