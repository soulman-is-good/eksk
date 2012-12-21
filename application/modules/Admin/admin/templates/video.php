
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Видео",
            'labels'=>array(
//                'image'=>array('@value'=>'Иконка','@wrapper'=>'<img src="/uploads/Shop_Category/{@image}" />'),
                'created_at'=>array('@value'=>'Дата','@wrapper'=>'{{=$modules->date()}}'),
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/video/edit/{@id}">{@title}</a>'),
                'status'=>'Видимость',
                'tomain'=>'На главную'
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>