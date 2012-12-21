<?php

?>
<div class="x3-main-content">
    
<div id="x3-header" x3-layout="header"><a href="/admin/<?=$action?>"><?=$moduleTitle;?></a> > Списком</div>
<div class="x3-functional" x3-layout="functional"><?=$functional?></div>
<div id="x3-buttons" x3-layout="buttons"><br/></div>

    <div id="x3-container">
        <form method="post" action="/manufacturer/savelist">
            <fieldset>
            <legend>Список производителей</legend>
            </fieldset>
            <div><span class="info">Каждый производитель должен быть на новой строке</span></div>
            <textarea id="drop" placeholder="вставлять сюда" name="list" rows="1" cols="50" style="border: 1px dashed #aaa;background: #eAeAEA" ></textarea>
            <br/>
            <button type="submit">Сохранить</button>
        </form>
    </div>
</div>
<div class="x3-paginator" x3-layout="paginator"><?=$paginator?></div>
<script type="text/javascript">
    $('#drop').keyup(function(e){
        var c = e.keyCode?e.keyCode : (e.which?e.which:null);
        if(e.ctrlKey && c==86){
            var L = $('<div />').css({'position':'fixed','left':'0','top':'0','width':$(window).width()+'px','height':$(window).height()+'px','background':"rgba(200,200,200,0.6) url(/application/modules/Admin/images/loader.gif) no-repeat 50% 50%"})
            $('body').append(L);
            $.post('/manufacturer/savelist',{'list':$(this).val()},function(m){
                L.remove();
                if(m==null) return;
                for(i in m.errors){
                    $.growl(m.errors[i]);
                }
                var T = $('<table />');
                for(i in m.objs){
                    var tr = $('<tr />');
                    var obj = m.objs[i];
                    if(obj['image']!=null)
                        tr.append($('<td />').append($('<img />').attr({'src':'/uploads/Manufacturer/'+obj['image']})));
                    else
                        tr.append($('<td />').append('&nbsp;'));
                    tr.append($('<td />').append($('<td />').html(obj['title'])));
                    tr.append($('<td />').append($('<td />').append($('<img />').data('mid',obj['id']).attr('src','/images/cross.png').click(function(){
                        $.get('/admin/manufacturer/delete/'+$(this).data('mid'));
                    }))));
                    
                }
            },'json').error(function(e){
                if(typeof console !='undefined')
                    console.log(e)
                L.remove()
                $.growl('Ошибка загрузки.');
            })
        }
    })
</script>