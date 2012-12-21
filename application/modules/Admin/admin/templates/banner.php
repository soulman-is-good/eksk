<div class="x3-submenu">
    <a href="/admin/shop">Товары</a> :: <a href="/admin/shopgroup">Группы</a> :: <span>Категории</span>
</div>
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Баннеры",
            'labels'=>array(
                'type'=>array('@value'=>'Размещение','@wrapper'=>'{{=$modules->types[$modules->type]}}'),
                'starts_at'=>array('@value'=>'Срок','@wrapper'=>'{{="<b>с ".date("d.m.Y",$modules->starts_at)." по ".date("d.m.Y",$modules->ends_at)."</b>"}}'),
                'file'=>array('@value'=>'Файл','@wrapper'=>'{{=$modules->printFile()}}'),
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/banner/edit/{@id}">{@title}</a>'),
                'clicks'=>array('@value'=>'Клики','@wrapper'=>'{@clicks}'),
                'status'=>'Видимость'
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>'',
            'functional'=>''            
            )
        )?>