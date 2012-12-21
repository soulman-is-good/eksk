<?=$this->renderPartial("@views:admin:templates:default.kansha.php",
        array(
            'modules'=>$modules,
            'moduleTitle'=>$moduleTitle,
            'labels'=>array(
                'image'=>array('@value'=>'Логотип','@wrapper'=>'<a href="/admin/company/edit/{@id}"><img src="/uploads/Company/128x64xh/{@image}" /></a>'),
                'title'=>array('@value'=>'Название','@wrapper'=>'<a href="/admin/company/edit/{@id}">{@title}</a>'),
                'isfree'=>array('@value'=>'Платный','@wrapper'=>'<img src="/images/admin/status{@isfree}.gif" />'),
                'status'=>'Видимость',
                'isfree'=>array('@value'=>'Платный пакет','@wrapper'=>'<img width="32" src="/images/{{=$modules->isfree?\'gstar\':\'ostar\'}}.png" />'),
                'weight'=>'Порядок',
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
    $('.action-payment a').each(function(){
        var self = $(this);
        var id = self.attr('href').split('/').pop();
        self.click(function(){
            $('<div title="Произвести оплату" />').append($('<form />')
            .append('<input type="hidden" name="Bill[company_id]" value="'+id+'" />')
            .append('Вид платежа:<br/><select name="Bill[type]"><option value="0">Услуги<option value="1">Прайс листы<option value="2">Распродажи</select><br/>')
            .append('Документ:<br/><input type="text" name="Bill[document]" /><br/>')
            .append('Сумма:<br/><input type="text" name="Bill[paysum]" value="0" /><br/>')
            .append('Дата оплаты:<br/>').append($('<input type="text" name="Bill[created_at]" value="<?=date("d.m.Y")?>" />').datepicker({'dateFormat':'dd.mm.yy','dayNamesMin':['Вс.','Пн.','Вт.','Ср.','Чт.','Пт.','Сб.'],'monthNames':['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь']})).append('<br/>')
            .append('Дата окочания:<br/>').append($('<input type="text" name="Bill[ends_at]" value="<?=date("d.m.Y",time()+2592000)?>" />').datepicker({'dateFormat':'dd.mm.yy','dayNamesMin':['Вс.','Пн.','Вт.','Ср.','Чт.','Пт.','Сб.'],'monthNames':['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь']})).append('<br/>')
            ).dialog({
                modal:true,
                onClose:function(){
                    $(this).dialog('destroy');
                },
                buttons:{
                    'Оплатить':function(){
                        $.post('/company_Bill/update',$(this).find('form').serialize(),function(m){
                            if(m=='OK'){
                                $.growl('Сохранено!');
                                location.reload();
                            }else
                                $.growl('Ошибка!');
                        })
                        $(this).dialog('close');
                    },
                    'Отмена':function(){
                        $(this).dialog('close');
                    }
                }
            })
            return false;
        })
    })
</script>