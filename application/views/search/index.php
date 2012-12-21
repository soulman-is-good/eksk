<div class="content jobs">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Поиск по сайту');?></h1>
        <form method="get" action="/search.html">
            <div class="row">
                <?if(X3::user()->search!==null && mb_strlen(X3::user()->search,$this->encoding)<4):?>
                <div style="color: #d63131"><?=X3::translate('Нужно ввести более двух символов');?></div>
                <?endif;?>
                <input type="text" value="<?=X3::user()->search?>" name="q" id="searchWord" />
            </div>
            <div class="row">
                <button style="margin-left: 160px;" type="submit"><i>&nbsp;</i><?=X3::translate('Искать');?><b>&nbsp;</b></button>
            </div>
        </form>
    </div>
</div>