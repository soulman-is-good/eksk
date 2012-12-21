<div class="x3-submenu">
    <?=$modules->menu()?>
</div>

<?
if(isset($_GET['parent_id']))
$labels = array(
//                'short_title'=>'Кратко',
                'weight'=>'Порядок',
                'title'=>array('@value'=>'Заголовок','@wrapper'=>'<a href="/admin/page/edit/{@id}">{@title}</a>'),
                'status'=>'Видимость',
                'onmain'=>'На главной',
            );
else
$labels = array(
//                'parent_id'=>array('@value'=>'Родитель','@wrapper'=>'<a href="/admin/page/parent_id/{@parent_id}">{{=$modules->parentM()->short_title}}</a>'),
//                'short_title'=>'Кратко',
                'weight'=>'Порядок',
                'title'=>array('@value'=>'Заголовок','@wrapper'=>'<a href="/admin/page/edit/{@id}">{@title}</a>'),
                'status'=>'Видимость',
                'onmain'=>'На главной',
            );
echo $this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>$labels,
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>