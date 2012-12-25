<?php

?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" id="add_admin" href="#admin/add.html">Добавить администратора</a></div>
        </div>
        <h1><?=X3::translate('Администраторы');?></h1>
    </div>
    <div class="content">
        <div class="stats">
            <?=X3::translate('Администраторов');?>: <?=$count?>
        </div>
        <table class="admin-list">
            <?foreach($models as $model):?>
            <tr>
                <td class="ava"><img src="/images/default.png" width="100" alt="" /></td>
                <td class="name"><a href="/user/<?=$model->id?>.html"><?=$model->name?> <?=$model->surname?></a></td>
                <td class="ops">
                    <a href="/user/send/id/<?=$model->id?>.html"><span>Написать сообщение</span></a>
                    <a href="/user/message/id/<?=$model->id?>.html"><span>Блокировать</span></a>
                    <a href="/user/delete/id/<?=$model->id?>.html"><span>Удалить</span></a>
                </td>
            </tr>
            <?endforeach;?>
        </table>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script type="text/html" id="form_tmpl">
    <form method="post" action="/admin/send.html">
        <div class="errors" style="display:none"></div>
        <table class="eksk-form">
            <tr>
                <td class="label">
                    <label for="email">E-mail</label>
                </td>
                <td class="field">
                    <input type="text" name="email"  />
                </td>
            </tr>
        </table>
    </form>
</script>
<script>
    $(function(){
        $('#add_admin').click(function(){
            var eform = $($('#form_tmpl').html());
            $.dialog(eform,'<?=X3::translate('Добавление администратора');?>',{caption:'<?=X3::translate('Добавить');?>',callback:function(){
                $.loader();
                var self = this;
                var action = eform.attr('action');
                $.post(action,eform.serialize(),function(m){
                    $.loader();
                    eform.find('.errors').css('display','none').html('');
                    if(m.status == 'error'){                        
                        eform.find('.errors').css('display','block').html(m.message);
                    }else{
                        self.close()
                        $.dialog(m.message,'<?=X3::translate('Добавление администратора');?>',{callback:function(){this.close()},caption:'Закрыть'});
                    }
                },'json').error(function(){
                    $.loader();
                    eform.find('.errors').css('display','block').html('<?=X3::translate('Ошибка в системе. Попробуйте позднее.');?>')
                })
                return false;
            }});
            return false;
        })
    })
</script>