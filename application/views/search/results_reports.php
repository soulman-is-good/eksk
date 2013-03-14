<?php
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
            <?/*<div class="wrapper inline-block"><a class="button inline-block" id="add_admin" href="#admin/add.html"><?=X3::translate('Написать сообщение')?></a></div>*/?>
        </div>
        <h1><?=X3::translate('Результаты поиска');?></h1>
    </div>
    <div class="content">
        <div class="stats"><em>
            <?if($cnt>0):?>
            <?=X3::translate('Найдено');?>: <?=$cnt?>
            <?else:?>
            <?=X3::translate('Ничего не найдено');?>
            <?endif;?></em>
        </div>
        <?if($cnt>0):?>
        <table width="100%" class="search-results">
            <tr><td width="100%" class="fc">
        <table class="admin-list">
            <?$i=1;while($m = mysql_fetch_assoc($models)):
                $u = array('id'=>$m['id'],'name'=>$m['name'],'surname'=>$m['surname'],'kskname'=>$m['kskname'],'ksksurname'=>$m['ksksurname'],'image'=>$m['image']);
                $user = new User();
                $user->acquire($u);
                ?>
            <tr>
                <td class="ava"><a href="/message/with/<?=$user->id?>.html"><img src="<?=$user->avatar?>" width="100" alt="" /></a></td>
                <td class="name" width="150"><a href="/message/with/<?=$user->id?>.html"><?=$user->fullname?></a></td>
                <td class="text">
                    <a href="/message/with/<?=$user->id?>.html"><?=Search::highlight($m['title'])?></em></a>
                </td>
            </tr>
            <?endwhile;?>
        </table>
            </td></tr>
        </table>
        <?endif;?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>