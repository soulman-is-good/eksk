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
<ul>
<?if($P->page == 0):?>
    <li><span>1</span></li>
<?else:?>
        <li><a <?=($P->page==1?'rel="prev"':'')?> href="/<?=$P->url?>/page/1.<?=X3::app()->request->suffix?>">1</a></li>
<?endif;?>
        <?if($min>1):?>
            <li><b>...</b></li>
        <?endif;?>
        <?for($i=$min;$i<$max;$i++):?>
        <?if($i==$P->page):?>
        <li><span><?=$i+1?></span></li>
        <?else:?>
        <li><a <?=($i==$P->page-1)?'rel="prev"':''?><?=($i==$P->page+1)?'rel="next"':''?> href="/<?=$P->url?>/page/<?=$i+1?>.<?=X3::app()->request->suffix?>"><?=$i+1?></a></li>
        <?endif;?>
        <?endfor;?>
        <?if($max<$P->pages-2):?>
            <li><b>...</b></li>
        <?endif;?>
<?if($P->page == $P->pages-1):?>
        <li><span><?=$P->pages?></span></li>
        <li><a rel="prev" href="/<?=$P->url?>/page/<?=$P->page?>.<?=X3::app()->request->suffix?>"><i>&larr;</i><?=X3::translate('Предыдущая страница');?></a></li>
<?else:?>
        <li><a <?=($P->page==$P->pages-2)?'rel="next"':''?> href="/<?=$P->url?>/page/<?=$P->pages?>.<?=X3::app()->request->suffix?>"><?=$P->pages?></a></li>
        <?if($P->page>0):?>
        <li><a rel="prev" href="/<?=$P->url?>/page/<?=$P->page?>.<?=X3::app()->request->suffix?>"><i>&larr;</i><?=X3::translate('Предыдущая страница');?></a></li>
        <?endif;?>
        <li><a rel="next" href="/<?=$P->url?>/page/<?=$P->page+2?>.<?=X3::app()->request->suffix?>"><?=X3::translate('Следующая страница');?><i>→</i></a></li>
<?endif;?>
</ul>