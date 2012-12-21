<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Рассылка",
            'labels'=>array(
                'sent_at'=>array('@value'=>'Последняя отправка','@wrapper'=>'{{=$modules->sent_at>0?date("d.m.Y H:i",$modules->sent_at):\'Никогда\'}}'),
                'name'=>'ID',
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/notify/edit/{@id}">{@title}</a>'),
                'from'=>'От кого',
                'to'=>'Кому',
                'status'=>'Слать',
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>