<div class="content video" itemscope itemtype="http://schema.org/ImageGallery">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:news.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Фотографии');?></h1>
        <?foreach($models as $model):
            if(!is_file("uploads/Photo/$model->image")) 
                continue;
            $image = "/uploads/Photo/230x139xf/$model->image";
            ?>
        <div class="item" itemscope itemtype="http://schema.org/ImageObject">
            <a itemprop="url" class="photo" href="/uploads/Photo/<?=$model->image?>" target="_blank" title="<?=addslashes($model->title)?>">
                <time datetime="<?=date("Y-m-d",$model->created_at)?>" itemprop="dateCreated"><?=I18n::date($model->created_at)?></time>
                <img itemprop="image" alt="" title="<?=$model->title?>" src="<?=$image?>" />
            </a>
        </div>
        <?endforeach;?>
        <div class="share42init" style="margin:25px 0 5px"></div>
        <script type="text/javascript" src="<?=X3::app()->baseUrl?>/share42/share42.js"></script>        
        <div class="navi">
            <?=$paginator?>
        </div>
    </div>
</div>
<script>
$(function(){
    var dialog = null;
    $('.photo').each(function(){
        $(this).click(function(){
            if(dialog!=null)
                dialog.close();
            var src = $(this).attr('href');
            var title = $(this).attr('title');
            var img = $('<img />').css({'visibility':'hidden'});
            var content = $('<div />').css({'background':'url(/images/preloader.gif) no-repeat 50% 50% #FFF'});
            content.append(img);
            dialog = $.dialog(content,title);
            img.load(function(){
                content.css('background','none');
                if(img.width()>$(window).width())
                    img.width($(window).width());
                img.css({'visibility':'visible'})
                dialog.setSize(img.width()+20,img.height()+20);
                dialog.setRelativePosition('center');
            }).attr('src',src)
            return false;
        })
    })
})
</script>