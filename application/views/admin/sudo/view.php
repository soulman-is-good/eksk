<?php
$title = $model->moduleTitle();
$pk = $model->getTable()->getPK();
$fields = $model->_fields;
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <div class="wrapper inline-block"><a class="button inline-block" id="send_message" href="/admin/create/module/<?=$class?>.html">Добавить</a></div>
        </div>
        <h1><?=$title?></h1>
    </div>
    <div class="content">
        <table class="admin-list">
            <?foreach($fields as $name=>$field):?>
            <tr>
                <td><?=$model->fieldName($name)?></td>
                <td>
                    <?php
                    $dataType = strtolower(trim(preg_replace("/\[.+?\]/","",$model->_fields[$name][0])));
                    switch($dataType){
                        case "datetime":
                            echo date("d.m.Y H:i:s",$model->$name);
                            break;
                        default:
                            $method = ucfirst($name);
                            $method = "get{$method}Label";
                            if(method_exists($model, $method)){
                                echo call_user_func(array($model,$method));
                            }else
                                echo $model->$name;
                    }
                    ?>
                </td>
            </tr>
            <?endforeach;?>
        </table>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>