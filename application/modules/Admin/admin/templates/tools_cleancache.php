<style>
    .file {
        padding:10px 20px;
        background: #fff;
    }
    .file:nth-child(odd){
        background: #fdf5ce;
    }
    .file .title {
        margin: 10px 0 0 0;
        float:left;
    }
    .title span {
        font:italic 11px Verdana,serif;
        color:#4D357D;
        margin-right:10px;
    }
    .cross {
        float:right;
    }
</style>
<div class="x3-submenu">
    <?=$modules->menu('cleancache')?>
</div>
   <div class="x3-main-content">
       <div id="x3-buttons" x3-layout="buttons" style="float:right"><a href="/admin/tools/cleancache/yes" class="red">Очистить кэш</a></div>
        <div id="x3-header" x3-layout="header">Кэш</div>
        <div class="x3-paginator" x3-layout="paginator"></div>
        <div class="x3-functional" x3-layout="functional"></div>
        <div id="x3-container">
            <br/>
            <? foreach ($files as $file): ?>
            <div class="file">
                <div class="title"><span><?=date("d.m.Y H:i:s",$file['time'])?></span><a href="#" title="Показать"><?=$file['filename']?></a></div>
                <div class="cross"><a href="#<?=$file['filename']?>" title="Удалить"><img src="/application/modules/Admin/images/delete.gif" /></a></div>
                <br clear="both"/>
            </div>
            <? endforeach; ?>
        </div>
    </div>
<script type="text/javascript">
    var last = null;
    $(function(){
        $('.title a').click(function(){
            var self = $(this);
            $.post('/Admin_Tools/Showfile',{'filename':'application/cache/'+$(this).text()},function(m){
                if(m!=''){
                   var ta = $('<textarea />').val(m);
                   $('<div />').append(ta).dialog({modal:true,width:1000,height:600,buttons:{
                           'Сохранить':function(){
                               $.post('/Admin_Tools/savefile',{'filename':'application/cache/'+self.text(),'text':last.getData()},function(m){
                                    if(m=='OK') 
                                        $.growl('Сохранено','ok');
                               })
                               $(this).dialog('destroy');   
                           },
                           'Отменить':function(){
                               $(this).dialog('destroy');
                           }
                   }});
                   last = CKEDITOR.replace( ta[0] );
                }
            })
            return false;
        })
        $('.cross a').click(function(){
            var file = $(this).attr('href').replace(/^#/,'');
            var self = $(this);
            $.post('/Admin_Tools/Deletefile',{'filename':'application/cache/'+file},function(){
                self.parent().parent().fadeOut(function(){$(this).remove()});
            });
        });
    })
</script>