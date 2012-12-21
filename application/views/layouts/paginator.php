<?php
$min = 1;
$max = $P->pages - 1;

if($P->page - 2 > 1){
    $min = $P->page-2;
    $max = $min + $P->radius;
}else
    $max = $P->radius;
if($P->pages - $P->page < $P->radius+1) {
    $min = $P->pages - $P->radius;
    $max = $P->pages - 1;
}
if($max>$P->pages - 1)
    $max = $P->pages - 1;
if($min<1)
    $min = 1;
?>
<?if($P->page == 0):?>
        <span>1</span>
<?else:?>
        <a <?=($P->page==1?'rel="prev"':'')?> href="/<?=$P->url?>/page/1.<?=X3::app()->request->suffix?>">1</a>
<?endif;?>
        <?if($min>1):?>
            <b>...</b>
        <?endif;?>
        <?for($i=$min;$i<$max;$i++):?>
        <?if($i==$P->page):?>
        <span><?=$i+1?></span>
        <?else:?>
        <a <?=($i==$P->page-1)?'rel="prev"':''?><?=($i==$P->page+1)?'rel="next"':''?> href="/<?=$P->url?>/page/<?=$i+1?>.<?=X3::app()->request->suffix?>"><?=$i+1?></a>
        <?endif;?>
        <?endfor;?>
        <?if($max<$P->pages-2):?>
            <b>...</b>
        <?endif;?>
<?if($P->page == $P->pages-1):?>
        <span><?=$P->pages?></span>
<?else:?>
        <a <?=($P->page==$P->pages-2)?'rel="next"':''?> href="/<?=$P->url?>/page/<?=$P->pages?>.<?=X3::app()->request->suffix?>"><?=$P->pages?></a>
        <?if($P->page>0):?>
        <a rel="prev" href="/<?=$P->url?>/page/<?=$P->page?>.<?=X3::app()->request->suffix?>"><?=X3::translate('Предыдущая страница');?></a>
        <?endif;?>
        <a rel="next" href="/<?=$P->url?>/page/<?=$P->page+2?>.<?=X3::app()->request->suffix?>"><?=X3::translate('Следующая страница');?></a>
<?endif;?>