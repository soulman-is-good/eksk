<?php
    if($inner)
        $models = News::get(array('@condition'=>array('status'),'@order'=>'created_at DESC','@limit'=>3));
    else
        $models = News::get(array('@condition'=>array('status','onmain'),'@order'=>'created_at DESC','@limit'=>3));
    if($models->count()>0):
    $top = Menu::num_rows(array('status','parent_id'=>null)) * 25 + 178;    
?>
    <div class="widget"<?=($inner)?'style="margin-top:'.$top.'px"':''?>>
        <h2><?=X3::translate('Что нового');?></h2>
        <div class="widget_body">
            <?foreach($models as $model):?>
            <div class="news_title"><a href="/news/<?=$model->id?>.html"><?=$model->title?></a></div>
            <div class="news_text">
                <?=$model->content?>
            </div>
            <?endforeach;?>
        </div>
        <a class="more_link" href="/news.html"><?=X3::translate('Показать все записи');?></a>        
    </div>
<?endif;?>