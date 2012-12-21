<div class="content page">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=$model->title?></h1>
        <article><?=$model->text?></article>
    </div>
</div>