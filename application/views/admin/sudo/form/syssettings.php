<?php
$type = $model->type;
$model->_fields['value'][0] = $type;
foreach (X3::app()->languages as $lang) {
    $attr = 'value_'.$lang;
    $model->_fields[$attr][0] = $type;
}
$fields = array(
            'value'=>'Значение',
        );
if($model->getTable()->getIsNewRecord()){
    $model->_fields['type'][0] = 'enum["string","content","text","html","email","integer","datetime","boolean"]';
    $fields = array(
            'title'=>'Название',
            'name'=>'ID',
            'type'=>'Тип данных',
            'value'=>'Значение',
        );
}
echo $this->renderPartial('@views:admin:sudo:form.php',array('model'=>$model,'class'=>$class,'fields'=>$fields));