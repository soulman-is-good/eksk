<?php
$answers = explode('||',$model->answer);
$total = X3::db()->count("SELECT id FROM vote_stat WHERE vote_id=$model->id");
?>
<div class="eksk-wnd">
    <div class="head">
        <div class="buttons">
        </div>
        <h1><?= X3::translate('Результаты опроса'); ?></h1>
    </div>
    <div class="content">
        <div id="ch_theme">
            <a href="/vote/"><i>←</i><?=X3::translate('Вернуться к списку опросов');?></a>
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
        </div>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $(function(){
        $('.inside').each(function(){
            var w = $(this).data('width');
            $(this).css('width',w);
        })
    })
</script>