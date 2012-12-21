<?php
if(is_file(X3::app()->basePath.'/redirects.txt'))
    $redirects = file_get_contents(X3::app()->basePath.'/redirects.txt');
else
    $redirects = '';
?>
<div class="x3-submenu">
    <?=$modules->menu('redirects')?>
</div>
   <div class="x3-main-content">
        <div id="x3-buttons" x3-layout="buttons"></div>
        <div id="x3-header" x3-layout="header">Редиректы</div>
        <div class="x3-paginator" x3-layout="paginator"></div>
        <div class="x3-functional" x3-layout="functional"></div>
        <div id="x3-container">
            <br/>
            <form method="post">
                <button type="submit">Сохранить</button>
                <textarea name="redirects" style="width:100%;height:500px;font-family: Tahoma; font-size:14px;line-height:1.5"><?=$redirects?></textarea>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>