<style type="text/css">
    .formtable td{
        height: 35px;
    }
    .formtable .fieldname{
        width:157px;
        font:20px Tahoma;
        color:#464646;
    }

    .formtable .field input[type="text"] {
        display:block;
        font:16px Tahoma;
        border: 1px solid #848181;
        padding:9px;
        width:100px;
    }
    .formtable .field textarea {
        display:block;
        font:16px Tahoma;
        border: 1px solid #848181;
        padding:9px;
        width:100px;
    }
    .formtable .required {
        color:#ff0000;
        padding-left: 10px;
    }
    .formtable .error {
        color:#ff0000;
        padding-left: 12px;
        font:12px Tahoma;
        font-style: italic;
    }
</style>
<div class="x3-main-content" style="">
    <div id="x3-buttons" x3-layout="buttons"></div>
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle ?></a> > Настройка
    </div>
    <div class="x3-paginator" x3-layout="paginator"></div>
    <div class="x3-functional" x3-layout="functional"></div>
    <div id="x3-container"> 
    <?php
    $value = explode(" ",$model->value);
    $time = SysSettings::getValue('LastMailer', 'integer', 'Последняя рассылка', '[INVISIBLE]', '0');
    $time = $time==0?'не осуществлялась':date("d.m.Y",$time);
    ?>
<form method="post" action="/admin/subscribe/conf" id="subscribeconf-form">
    <h2 style="margin-top:0">Период <span style="font-size:11px;color:#cacaca">Последняя авто.рассылка: <i><?=$time?></i></span></h2>    
<table class="formtable">
    <tr>
        <td style="height:24px;font-size:16px;">День</td>
        <td style="height:24px;font-size:16px;">Месяц</td>
        <td style="height:24px;font-size:16px;">Неделя</td>
        <td style="color:#848181;font-size:10px;">*-каждый день месяца(1-31), месяц(1-12), день недели(1-7).<br/>* * * - каждый день<br/>1 * * - каждый месяц первого числа<br/>0 0 0 - Остановить рассылку</td>
    </tr>
    <tr>
        <td class="field"><input type="text" value="<?=$value[0]?>" name="value[0]"></td>
        <td class="field"><input type="text" value="<?=$value[1]?>" name="value[1]"></td>
        <td class="field"><input type="text" value="<?=$value[2]?>" name="value[2]"></td>
        <td class="error">&nbsp;</td>
    </tr>
</table>
    <h2>Шаблон ручной рассылки</h2>
    <table class="formtable">
    <tr id="asas-text">       
        <td class="field"><?=X3_Html::form_tag('textarea',array('cols'=>'150','rows'=>'10','style'=>'width:700px','id'=>'Templt','name'=>'template','%content'=>$templ->value))?></td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>        
    </table>
    <input type="hidden" value="1" name="soso" />
    <button type="submit">Сохранить</button>
</form>
</div>
</div>
<script type="text/javascript">
    new CKEDITOR.replace("Templt");
</script>