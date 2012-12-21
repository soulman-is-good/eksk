<?php
$clients = X3::db()->query("SELECT * FROM data_company");
$count = ceil(mysql_num_rows($clients) / 3);
$letter = SysSettings::getValue('letterTemplate', 'text', 'Шаблон письма рассылки', 'Шаблоны', '');
?>
<style>
    #x3-container fieldset ul {
        list-style: none;
        display:inline-block;
        width:30%;
    }
</style>
<div class="x3-main-content">

    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle; ?></a> > Оповестить клиентов</div>
    <div class="x3-functional" x3-layout="functional"><?= $functional ?></div>
    <div id="x3-buttons" x3-layout="buttons"><br/></div>

    <div id="x3-container">
        <form method="post">
            <fieldset>
                <legend>Список клиентов</legend>
                <a href="#" onclick="$(this).parent().find('input').attr('checked',1);return false;">Выделить все</a> / <a href="#" onclick="$(this).parent().find('input').removeAttr('checked');return false;">Снять выделение</a>
                <br clear="left" />
                <ul>
                    <? $i = 0;
                    while ($c = mysql_fetch_assoc($clients)): ?>
                        <li><input type="checkbox" checked="checked" name="Company[]" value="<?= $c['id'] ?>"  /><?= $c['title'] ?></li>
                        <? if ($i > 0 && $i % $count == 0) echo '</ul><ul>' ?>
    <? $i++;
endwhile; ?>
                </ul>
                <br clear="left" />
            </fieldset>
            Заголовок письма:<br/>
            <input type="text" name="title" style="width:100%"/>
            <br/>
            <br/>
            Текст письма:
            <div style="padding:4px;"><span class="info">В письме Вы можете использовать такие ключевые слова:<br/>
                    [COMPANY] - название компании
            </span></div>
            <textarea name="letter" id="letter" style="width:100%;height:600px"><?=$letter?></textarea>
            <br/>
            
            <button type="submit">Отправить</button>
        </form>
    </div>
</div>
<script>
    //if(typeof CKEDITOR.instances['letter'] != 'undefined')
    //    delete(CKEDITOR.instances['letter']);
    CKEDITOR.replace( 'letter' );
</script>