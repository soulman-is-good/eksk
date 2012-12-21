<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>array(
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/service/edit/{@id}">{@title}</a>'),
                'status'=>'Видимость'
                ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>