<div class="x3-submenu">
    <span>Пользовательское меню</span> :: <a href="/admin/admin_menu">Административное меню</a>
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