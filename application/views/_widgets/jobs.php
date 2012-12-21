<?php
if($inner)
    $models = Jobs::get(array('@condition'=>array('status'),'@order'=>'created_at DESC','@limit'=>3));
else
    $models = Jobs::get(array('@condition'=>array('status','onmain'),'@order'=>'created_at DESC','@limit'=>3));
    if($models->count()>0):
$top = Menu::num_rows(array('status','parent_id'=>null)) * 25 + 178;
?>
    <div class="widget"<?=($inner)?' style="margin-top:'.$top.'px"':''?>>
        <h2><?=X3::translate('Вакансии');?></h2>
        <div class="widget_body">
            <?foreach($models as $model):?>
            <div class="news_title"><a href="/jobs/<?=$model->id?>.html"><?=$model->title?></a></div>
            <div class="news_text">
                <?=$model->content?>
            </div>
            <?endforeach;?>
        </div>
        <?if(!$inner):?>
        <span class="loupe"><a class="more_link" href="/search/jobs.html"><?=X3::translate('Поиск вакансий');?></a></span>
        <?endif;?>
    </div>
<?endif;?>