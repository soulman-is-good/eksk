<div class="content news" itemscope itemtype="http://schema.org/VideoObject">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:news.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1 itemprop="name"><?=$model->title?></h1>
        <article itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
                <meta itemprop="thumbnail" content="/uploads/Video/230x139/<?=$model->preview?>" />
                <?=$model->code?>
        </article>
        <br/>
        <div class="share42init" style="margin:25px 0"></div>
        <script type="text/javascript" src="<?=X3::app()->baseUrl?>/share42/share42.js"></script>                
        <a class="more_link" href="/video.html"><?=X3::translate('Посмотреть все видео');?></a>
    </div>
</div>