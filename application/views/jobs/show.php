<div class="content news" itemscope itemtype="http://schema.org/JobPosting">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1 itemprop="name"><?=$model->title?></h1>
        <article itemprop="skills">
            <p><strong><?=X3::translate('Город');?>:</strong> <?=$model->city?></p>
            <p><strong><?=X3::translate('Сфера деятельности');?>:</strong> <?=$model->sphere?></p>
            <p><?=$model->text?></p>
        </article>
        <a class="more_link" href="/jobs.html"><?=X3::translate('Вернуться назад к результатам поиска');?></a>
    </div>
</div>