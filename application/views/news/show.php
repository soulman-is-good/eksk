<div class="content news" itemscope itemtype="http://schema.org/NewsArticle">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:video.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1 itemprop="name"><?=$model->title?></h1>
        <article itemprop="articleBody"><?=$model->text?></article>
        <div class="share42init" style="margin:25px 0"></div>
        <script type="text/javascript" src="<?=X3::app()->baseUrl?>/share42/share42.js"></script>        
        <a class="more_link" href="/news.html"><?=X3::translate('Показать все записи');?></a>
    </div>
</div>