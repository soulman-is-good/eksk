<div class="content news">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:video.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Что нового');?></h1>
        <?foreach($models as $model):?>
        <div class="item" itemscope itemtype="http://schema.org/NewsArticle">
            <time datetime="<?=date("Y-m-d",$model->created_at)?>" itemprop="dateCreated"><?=$model->date()?></time>
            <h2 itemprop="name"><a itemprop="url" href="/news/<?=$model->id?>.html" title="<?=addslashes($model->title)?>"><?=$model->title?></a></h2>
        </div>
        <?endforeach;?>
        <div class="navi">
            <?=$paginator?>
        </div>
    </div>
</div>