<?php
    $model = Page::get(array('@condition'=>array('status','onmain','name'=>'about_mag'),'@order'=>'weight, created_at DESC'),1);
    if($model!==null):
?>
    <div class="widget">
        <h2><?=$model->title?></h2>
        <div class="widget_body">
            <div class="news_text">
                <?=X3_String::create(strip_tags($model->text,"<b><strong><i><span><br>"))->carefullCut(520)?>&ctdot;
            </div>
        </div>
        <a class="more_link" href="/page/<?=$model->name?>.html"><?=X3::translate('Читать подробнее');?></a>
    </div>
<?endif;?>