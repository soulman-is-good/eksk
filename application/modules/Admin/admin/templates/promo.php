
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Промо фото",
            'labels'=>array(
                'image'=>array('@value'=>'Фото','@wrapper'=>'<img src="/uploads/Promo/100x100xf/{@image}" />'),
                'created_at'=>'Дата создания',
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/promo/edit/{@id}">{{=$module->realTitle()}}</a>'),
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