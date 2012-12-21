<style>
    table.formtable td{
        text-align: left;
    }
    .x3-errors{
        color:#FF0000;
    }
    .x3-main-content table input[type="text"], .x3-main-content table textarea {width:100%}    
</style>
<?php
$form = new Form($modules);
?>
    <div class="x3-main-content" style="">
        <div id="x3-buttons" x3-layout="buttons"></div>
        <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>">Видео</a> > <? if ($subaction == 'add'): ?>Добавить
            <? else: ?>
            Редактировать<? echo $modules->title; endif; ?>
        </div>
        <div class="x3-paginator" x3-layout="paginator"></div>
        <div class="x3-functional" x3-layout="functional"></div>
        <div id="x3-container"> 
            <div style="padding:10px;clear:both;">                
        <?= $form->start(array('action'=>'/admin/video/save')); ?>
                <?=$form->render();?>
        <?= $form->end();?>
            </div>
        </div>
    </div>
<script>
/**
 * Get preview
 */
$(function(){
    $('#Video_code').change(function(){
        var val = $(this).val();
        var id = false;
        if(/youtube/.test(val)){
            id = /youtube.com\/embed\/([^"]+)/.exec(val).pop();
            var img = "http://img.youtube.com/vi/"+id+"/hqdefault.jpg";
            $('<img />').attr({'alt':'Битая ссылка','src':img,'width':120}).insertBefore($('#Video_preview'))
            $('[name="Video[preview_url]"]').val(img)
            $.get('https://gdata.youtube.com/feeds/api/videos/'+id+'?v=2&alt=json',function(m){
                if($('#Video_title').val()==''){
                    $('#Video_title').val(m.entry.title.$t);
                    $('#Video_title_en').val(m.entry.title.$t);
                    $('#Video_title_kz').val(m.entry.title.$t);
                }
            },'json').error(function(){});
        }
    })
})
</script>