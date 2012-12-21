<?
$groups = X3::db()->fetchAll("SELECT `group` FROM `sys_settings` GROUP BY `group`");
if(!empty($groups)):
?>
<div class="x3-submenu">
    <?$c=count($groups);foreach($groups as $i=>$group):if($group['group']=='[INVISIBLE]')continue;?>
    <?if($group['group']==urldecode($_GET['group'])):?>
    <span><?=$group['group']?></span><?if($i<$c-1)echo' ::';?> 
    <?else:?>
    <a href="/admin/settings/group/<?=urlencode($group['group'])?>"><?=$group['group']?></a><?if($i<$c-1)echo' ::';?> 
    <?endif;?>
    <?endforeach;?>
</div>
<?
endif;
echo $this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>array(
                'title'=>'Назавние',
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''
            )
        )?>
