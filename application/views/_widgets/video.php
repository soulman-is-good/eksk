<?php
if($inner)
    $models = Video::get(array('@condition'=>array('status'),'@order'=>'created_at DESC','@limit'=>2));
else
    $models = Video::get(array('@condition'=>array('status','tomain'),'@order'=>'created_at DESC','@limit'=>2));
if($models->count()>0):
$top = Menu::num_rows(array('status','parent_id'=>null)) * 25 + 178;    
?>    
    <div class="widget"<?=($inner)?' style="margin-top:'.$top.'px"':''?>>
        <h2><?=X3::translate('Последние видео');?></h2>
        <div class="widget_body">
            <?foreach($models as $model):?>
            <div class="video_title"><a href="/video/<?=$model->id?>.html">
                    <img src="/uploads/Video/230x139xf/<?=$model->preview?>" />
                    <?=$model->title?>
            </a></div>
            <?endforeach?>
        </div>
        <a class="more_link" href="/video.html"><?=X3::translate('Посмотреть все видео');?></a>
    </div>
<?endif;?>