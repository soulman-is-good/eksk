<?php
$types = array(
    '*'=>'',
    'user'=>'пользователям',
    'admin'=>'администраторам',
    'ksk'=>'КСК',
);
$total = X3::db()->count("SELECT id FROM vote_stat WHERE vote_id=$model->id");
?>
<div class="eksk-wnd">
    <div class="head">
        <h1><?=X3::translate('Аналитические данные');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs="vote">
            <ul>
                <li><a href="/analytics/ksk.html"><?=X3::translate('КСК');?></a></li>
                <li><a href="/analytics/user.html"><?=X3::translate('Жильцы');?></a></li>
                <li><a href="#vote"><?=X3::translate('Опросы');?></a></li>
                <li><a href="/analytics.html"><?=X3::translate('Активность пользователей');?></a></li>
            </ul>
            <div class="tab" id="vote">
                <div class="stats">
                    <b><?=X3::translate('Результаты голосования');?>:</b> <a href="/uploads/excel/generate/vote<?=$model->id?>.xls" class="excel" target="_blank"><span><?=X3::translate('Экспорт в Excel');?></span></a>
                </div>
                <div id="ch_theme">
                    <div class="votelist mt-15">
                        <h4><?=nl2br($model->title)?></h4>
                        <table width="100%">
                            <tbody>
                                <?foreach($answers as $i=>$answer):
                                    $cnt = X3::db()->fetch("SELECT COUNT(0) cnt FROM vote_stat WHERE vote_id=$model->id AND answer='$i'");
                                    $per = $total>0?round($cnt['cnt']/$total * 100):0;
                                ?>
                                <tr>
                                    <th colspan="2">
                                        <span><?=$answer?></span>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="vote_stripe">
                                            <div class="inside" data-width="<?=$per?>%">
                                                <span><?=$cnt['cnt']?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="50">
                                        <span class="pr"><?=$per?>%</span>
                                    </td>
                                </tr>
                                <?endforeach;?>
                            </tbody></table>
                    </div>
                    <a href="/analytics/vote.html"><i>←</i><?=X3::translate('Вернуться к списку опросов');?></a>
                    <?php
                        $q = X3::db()->query("SELECT u.id, u.name, u.kskname, u.surname, u.ksksurname, u.image, v.answer FROM vote_stat v INNER JOIN user_address vv ON vv.id=v.address_id INNER JOIN data_user u ON u.id=vv.user_id WHERE vote_id='$model->id' GROUP BY u.id");
                        $cnt = mysql_num_rows($q);
                    ?>
                    <div>
                        <h4><?=X3::translate('Проголосовало пользователей');?>: <?=$cnt?></h4>
                        <table class="admin-list">
                            <?while($u = mysql_fetch_assoc($q)):
                                $user = new User();
                                $user->acquire($u);
                                ?>
                            <tr>
                                <td class="ava"><img src="<?=$user->avatar?>" width="100" alt="" /></td>
                                <td class="name"><a href="/user/<?=$user->id?>.html"><?=$user->fullname?></a></td>
                                <td class="ops"><em>Ответил</em><br/><b><?=$answers[$u['answer']]?></b></td>
                            </tr>
                            <?endwhile;?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow">&nbsp;</div>
</div>
<script>
    $(function(){
        $('.inside').each(function(){
            var w = $(this).data('width');
            $(this).css('width',w);
        })
    })
</script>