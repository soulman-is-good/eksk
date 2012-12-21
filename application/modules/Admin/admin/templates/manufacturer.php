<?=
$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"Товары",
            'labels'=>array(
                'rules'=>array('@value'=>'Правила','@wrapper'=>'{{=$modules->rules!=""?"Есть":"Нет"}}'),
                'image'=>array('@value'=>'Лого','@wrapper'=>'{{=empty($modules->image)?\'<a mid="{@id}" href="#@#" class="getlogo" name="{@name}">Подобрать</a>\':\'<a href="/admin/manufacturer/edit/{@id}"><img src="/uploads/Manufacturer/128x64xh/{@image}" /></a>\'}}'),
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/manufacturer/edit/{@id}">{@title}</a>'),
            ),
            'class'=>$class,
            'actions'=>$actions,
            'action'=>$action,
            'subaction'=>$subaction,
            'paginator'=>$paginator,
            'functional'=>''            
            )
        )?>
<script type="text/javascript">
    $(function(){
        $('.getlogo').each(function(){
            $(this).click(function(){
                var name = $(this).attr('name');
                var id = $(this).attr('mid');
                var self = $(this);
                $('#dddddd').dialog('destroy');
                $.get('/manufacturer/getimages',{'q':name+' logo'},function(m){
                    var div = $('<div />').attr({'title':'Выберите логотип','id':'dddddd'});
                    $(m).find('#results-wrap img').each(function(){
                        var src = $(this).attr('src');
                        var a = $('<a href="#"><img src="'+src+'" /></a>');
                        a.click(function(){
                            $.post('/manufacturer/linkimg',{src:src,name:name},function(m){
                                self.replaceWith('<a href="/admin/manufacturer/edit/'+id+'"><img src="/uploads/Manufacturer/128x64xh/'+m+'" /></a>')
                                div.dialog('close');
                            })
                            return false;
                        })
                        div.append(a);
                    })
                    div.dialog();
                })
                return false;
            })
        })
    })
</script>