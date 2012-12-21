<div class="x3-submenu">
    <a href="/admin/menu">Пользовательское меню</a> :: <span>Административное меню</span>
</div>
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>$labels,
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>'',
            'functional'=>''
            )
    )?>