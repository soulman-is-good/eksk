<div class="x3-submenu">
    <a href="/admin/region">Города</a> ::
    <a href="/admin/sphere">Сферы деятельности</a>
</div>
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Вакансии",
            'labels'=>array(
                'created_at'=>array('@value'=>'Название','@wrapper'=>'{{=I18n::date($modules->created_at)}}'),
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/jobs/edit/{@id}">{@title}</a>'),
                'status'=>'Видимость',
                'onmain'=>'На главную',
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>