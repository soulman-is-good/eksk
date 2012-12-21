<div class="content video" itemscope itemtype="http://schema.org/VideoGallery">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:news.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Видео');?></h1>
        <?foreach($models as $model):
            $image = '/images/video.default.jpg';
            if(is_file("uploads/Video/$model->preview")){
                $image = "/uploads/Video/230x139xf/$model->preview";
            }
            ?>
        <div class="item" itemscope itemtype="http://schema.org/Movie">
            <a itemprop="url" href="/video/<?=$model->id?>.html" title="<?=addslashes($model->title)?>">
                <img itemprop="image" alt="" title="<?=$model->title?>" src="<?=$image?>" />
                <h2 itemprop="name"><?=$model->title?></h2>
            </a>
        </div>
        <?endforeach;?>
        <div class="navi">
            <?=$paginator?>
        </div>
    </div>
</div>