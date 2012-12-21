<div class="content jobs">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Результаты поиска');?><sup><?=$cnt?></sup><a href="/search.html" class="more_link" style="text-transform: none;position:relative;top:-3px;margin-left:20px;"><?=X3::translate('Вернуться назад к поиску по сайту');?></a></h1>
        <?while($model = mysql_fetch_object($models)):
            $link = str_replace("[LINK]",$model->link,$data[$model->type]['link']);
            ?>
        <div class="item">
            <h2><a href="<?=$link?>" title="<?=addslashes($model->title)?>"><?=$model->title?></a></h2>
            <article><?=X3_String::create(strip_tags($model->text))->carefullCut(512)?></article>
        </div>
        <?endwhile;?>
        <br/>
        <div class="navi">
            <?=$paginator?>
        </div>
    </div>
</div>