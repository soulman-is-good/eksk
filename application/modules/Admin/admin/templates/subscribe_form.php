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
        width:459px;
    }
    .formtable .field textarea {
        display:block;
        font:16px Tahoma;
        border: 1px solid #848181;
        padding:9px;
        width:459px;
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
<?php
$form = new X3_Form($model);
echo $form->start();
if(!is_null($model->id))
    echo $form->hidden('id');
echo $form->hidden('key');
?>
<table class="formtable">
    <tr id="<?=strtolower($class)?>-title">
        <td class="fieldname"><?=$model->fieldName('email')?></td>
        <td class="field"><?=$form->input('email')?></td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="<?=strtolower($class)?>-name">
        <td class="fieldname"><?=$model->fieldName('name')?></td>
        <td class="field"><?=$form->input('name')?></td>
        <td class="required">*</td>
        <td class="error">&nbsp;</td>
    </tr>
    <tr id="<?=strtolower($class)?>-status">
        <td class="fieldname"><?=$model->fieldName('status')?></td>
        <td class="field"><?=$form->checkbox('status')?></td>
        <td class="required"></td>
        <td class="error">&nbsp;</td>
    </tr>
</table>
<?=$form->end();?>