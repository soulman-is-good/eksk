<?php
$promos = Promo::getNormalized(array('@condition'=>array('status'),'@order'=>'RAND()'),false,true);
if(($c = count($promos))>0):
?>
<div class="gallery">
    <div class="lozung">
        <h1><?=$promos[0]['title']?></h1>
        <div class="text"><?=$promos[0]['text']?></div>
        <a href="<?=$promos[0]['link']?>"><?=X3::translate('подробнее');?></a>
    </div>
    <div class="stack"><span></span></div>
    <div class="frame"><!-- --></div>
    <img id="preview" src="/uploads/Promo/<?=$promos[0]['image']?>" width="100%" alt="" title="<?=  addslashes($promos[0]['title'])?>" />
</div>
<?if($c>1):?>
<script>
    var promos = <?=json_encode($promos)?>;
    var current = 0;
    $(function(){
        var span = $('.stack span');
        for(i in promos){
            var promo = promos[i];
            var tmp = new Image();
            //preload
            $(tmp).attr({'src':'/uploads/Promo/'+promo.image});
            var img = $('<img />').attr({'src':'/uploads/Promo/140x105/'+promo.image,'title':promo.title,'alt':''}).data('idx',i);
            if(i == current) img.addClass('active');
            img.click(function(){
                if($(this).hasClass('active') || $(this).data('animating'))
                    return false;
                var i = $(this).data('idx');
                if(i == current)
                    return false;
                $(this).data('animating',true);
                $('.stack .active').removeClass('active');
                current = i;
                var promo = promos[i];
                var anim = $('<img />').css({position:'absolute','opacity':'0','z-index':3,width:'100%','left':'0','top':'0'}).attr('src','/uploads/Promo/'+promo.image);
                $('.gallery').append(anim);
                $('.lozung h1').fadeOut(function(){$(this).html(promo.title).fadeIn()});
                $('.lozung .text').fadeOut(function(){$(this).html(promo.text).fadeIn()});
                $('.lozung a').fadeOut(function(){$(this).attr('href',promo.link).fadeIn()});
                anim.animate({opacity:1},function(){
                    $('#preview').attr('src','/uploads/Promo/'+promo.image);
                    anim.remove();
                })
//                $('#preview').animate({opacity:0},function(){$(this).attr('src','/uploads/Promo/'+promo.image).animate({opacity:1})});
                $(this).addClass('active');
                $(this).data('animating',false);
                return false;
            })
            span.append(img);
        }
    })
</script>
<?endif;?>
<?endif;?>
<div class="main_widgets">
    <?=X3_Widget::run("@views:_widgets:news.php");?>
    <?=X3_Widget::run("@views:_widgets:text.php");?>
    <?=X3_Widget::run("@views:_widgets:video.php");?>
    <?=X3_Widget::run("@views:_widgets:jobs.php");?>
</div>
<div class="clear"><!-- --></div>
