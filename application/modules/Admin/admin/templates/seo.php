<div class="x3-submenu">
    <?php
    $titles = array();
    foreach (SeoHelper::$allow as $cls){
        $html = "";
        $i = X3_Module::getInstance($cls);
        if($cls == X3::user()->metaClass)
            $html = "<span>@</span>";
        else
            $html = '<a href="%%">@</a>';
        if(method_exists($cls, 'moduleTitle')){
             $html = str_replace ("@", $i->moduleTitle(),$html);
             if(property_exists($i, 'tableName'))
                $titles[$i->tableName] = $i->moduleTitle();
             else
                $titles[$cls] = $i->moduleTitle();
        }else{
             $html = str_replace ("@", $cls,$html);
             if($cls instanceof X3_Module_Table)
                $titles[$i->tableName] = $cls;
             else
                $titles[$cls] = $cls;
        }
        $link = X3::app()->request->parseUrl(array('module'=>$cls));
        echo str_replace ("%%", $link,$html) . " :: ";
    }
    ?>
</div>
<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>"СЕО",
            'titles'=>$titles,
            'labels'=>array(
                'table'=>array('@label'=>'Модуль','@wrapper'=>'{{=$titles[\'{@table}\']]}}'),
                'metatitle'=>'Мета-заголовок',
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
    var ops = <?=  json_encode($titles)?>;
    var div = null;
    var sel = $('<select />');
    sel.attr({'name':'Meta[table]'});
    for(i in ops){
        sel.append('<option value="'+i+'">'+ops[i]+'</option>');
    }
    $(function(){
        $('#add').click(function(){
            if(div == null)
                div = $('<div title="Добавить мета-инормацию" />')
                .append($('<form />')
                        .append($('<span />').css({'display':'inline-block','width':'100px'}).html('Модуль'))
                        .append(sel)
                        .append($('<span />').css({'display':'inline-block','width':'100px'}).html(''))
                        .append($('<input />').attr({'name':'Meta[metatitle]'}))
                )
                .dialog({modal:true,buttons:{
                        'Сохранить':function(){
                            $.post('/meta/save',div.find('form').serialize(),function(m){
                                if(m.status=='ERROR'){
                                    if(typeof m.message == 'object'){
                                        for(i in m.message)
                                            $.growl(m.message[i]);
                                    }else
                                        $.growl(m.message)
                                }else{
                                    location.refresh();
                                    $.growl('Сохранено!','ok');
                                    div.find('input').val('');
                                    div.find('textarea').val('');
                                    div.dialog('close');
                                }
                            },'json').error(function(){$.growl('Ошибка сохранения!')})
                        },
                        'Отмена':function(){
                            div.find('input').val('');
                            div.find('textarea').val('');
                            div.dialog('close');
                        }
                }});
            else
                div.dialog('open');
            return false;
        })
    })
</script>