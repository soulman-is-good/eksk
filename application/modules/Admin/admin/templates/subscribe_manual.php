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
        width:486px;
    }
    .formtable .field textarea {
        display:block;
        font:16px Tahoma;
        border: 1px solid #848181;
        padding:9px;
        width:486px;
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
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle ?></a> > Ручная расслыка
    </div>
    <div class="x3-paginator" x3-layout="paginator"></div>
    <div class="x3-functional" x3-layout="functional"></div>
    <div id="x3-container"> 
<form method="post" action="/subscribe/sendsub/type/manual" id="subscribesend-form">
<table class="formtable">    
    <tr id="send-title">
        <td class="fieldname">Тема письма</td>
        <td class="field"><input type="text" name="Send[title]" value="" /></td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="send-text">
        <td class="fieldname">Письмо</td>
        <td class="field"><?=X3_Html::form_tag('textarea',array('cols'=>'150','rows'=>'10','style'=>'width:700px','id'=>'Templt','name'=>'Send[body]','%content'=>SysSettings::getValue('SubscribeHandText', 'text', 'Шаблон ручной рассылки', 'Рассылка', '')))?></td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="send-users">
        <td class="fieldname">Подписчики</td>
        <td class="field">
            <span class="info">Чтобы выбрать несколько, удерживайте Ctrl</span><br/>
            <select multiple="multiple" size="10" name="Send[users][]" style="width:700px;height:200px">
                <?php
                $subs = Subscribe::get(array());
                ?>
                <? foreach ($subs as $sub): ?>
                <option <?if($sub->status):?>selected="selected"<?endif;?> value="<?=$sub->id?>"><?=$sub->email?></option>
                <? endforeach; ?>
            </select>                
        </td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4"><button type="submit">Разослать</button></td>
    </tr>
</table>
</form>
</div>
</div>
<script type="text/javascript">
    new CKEDITOR.replace("Templt");
</script>