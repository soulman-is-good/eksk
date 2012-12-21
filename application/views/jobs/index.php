<div class="content jobs">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Результаты поиска');?><a href="/search/jobs.html" class="more_link" style="text-transform: none;position:relative;top:-3px;margin-left:20px;"><?=X3::translate('Вернуться назад к поиску вакансий');?></a></h1>
        <?while($model = mysql_fetch_object($models)):?>
        <div class="item" itemscope itemtype="http://schema.org/JobPosting">
            <h2 itemprop="title"><a itemprop="url" href="/jobs/<?=$model->id?>.html" title="<?=addslashes($model->title)?>"><?=$model->title?></a></h2>
            <?/*<time datetime="<?=date("Y-m-d",$model->created_at)?>" itemprop="datePosted"><?=I18n::date($model->created_at)?></time>*/?>
            <meta content="<?=date("Y-m-d",$model->created_at)?>" itemprop="datePosted" />
            <article itemprop="experienceRequirements"><?=$model->content?></article>
            <meta itemprop="hiringOrganization" content="MAG group" />
            <meta itemprop="jobLocation" content="<?=$model->city?>" />
        </div>
        <?endwhile;?>
        <br/>
        <div class="navi">
            <?=$paginator?>
        </div>
    </div>
</div>