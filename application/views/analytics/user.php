<div class="eksk-wnd">
    <div class="head">
        <h1><?=X3::translate('Аналитические данные');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs="user">
            <ul>
                <li><a href="/analytics/ksk.html"><?=X3::translate('КСК');?></a></li>
                <li><a href="#user"><?=X3::translate('Жильцы');?></a></li>
                <li><a href="/analytics/vote.html"><?=X3::translate('Опросы');?></a></li>
                <li><a href="/analytics.html"><?=X3::translate('Активность пользователей');?></a></li>
            </ul>
            <div class="tab" id="user">
                <div class="stats">
                    <b><?=X3::translate('Жильцов');?>: <?=$count?></b> <a href="/uploads/excel/generate/user.xls" class="excel"><span><?=X3::translate('Экспорт в Excel');?></span></a>
                </div>
                <table class="admin-list">
                    <?foreach($models as $model):
                        //$info = X3::db()->fetch("SELECT * FROM user_settings WHERE user_id='$model->id'");
                        $profile = User_Settings::get(array('user_id'=>$model->id),1);
                        ?>
                    <tr>
                        <td class="ava"><img src="<?=$model->avatar?>" width="100" alt="" /></td>
                        <td class="name"><a href="/user/<?=$model->id?>.html"><?=$model->fullname?><?=$model->status==0?'<em>'.X3::translate('Не активирован').'</em>':''?></a></td>
                        <td class="ops">
                            <div class="line">
                            <a class="dash" href="#main-info"><span><?=X3::translate('Основная информация')?></span>
                            </a>
                            <div class="popup">
                                <div class="info-row">
                                    <?=$model->name?> <?=$model->surname?>
                                </div>
                                <div class="info-row">
                                    <?=X3::translate('Основная информация')?>:
                                </div>
                                <table width="100%">
                                <tr>
                                    <td><em><?=X3::translate('Пол');?>:</em></td>
                                    <td><?=X3::translate($model->gender)?></td>
                                </tr>
                                <?if($model->date_of_birth>0):?>
                                <tr>
                                    <td><em><?=X3::translate('Дата рождения');?>:</em></td>
                                    <td><?=  I18n::date($model->date_of_birth)?></td>
                                </tr>
                                <?endif;?>
                                <?if(trim($profile->about)!=''):?>
                                <tr>
                                    <td><em><?=X3::translate('О себе');?>:</em></td>
                                    <td><?= nl2br($profile->about)?></td>
                                </tr>
                                <?endif;?>
                                </table>
                            </div>
                            </div>
                            <div class="line">
                            <a class="dash" href="#contact-info"><span><?=X3::translate('Контактная информация')?></span>
                            </a>
                            <div class="popup">
                                <div class="info-row">
                                    <?=$model->name?> <?=$model->surname?>
                                </div>
                                <div class="info-row">
                                    <?=X3::translate('Контактная информация')?>:
                                </div>
                                <table width="100%">
                                <?if($profile->home != ''):?>
                                <tr>
                                        <td class="one">
                                                <em><?=X3::translate('Телефон')?>:</em>
                                        </td>
                                        <td>
                                                <span>+7 <?=$profile->home?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?if($profile->work != ''):?>
                                <tr>
                                        <td class="one">
                                                <em><?=X3::translate('Рабочий')?>:</em>
                                        </td>
                                        <td>
                                                <span>+7 <?=$profile->work?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?if($profile->mobile != ''):?>
                                <tr>
                                        <td class="one">
                                                <em><?=X3::translate('Мобильный')?>:</em>
                                        </td>
                                        <td>
                                                <span>+7 <?=$profile->mobile?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?if($profile->email != ''):?>
                                <tr>
                                        <td class="one">
                                                <em>E-Mail:</em>
                                        </td>
                                        <td>
                                                <span><?=$profile->email?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?if($profile->skype != ''):?>
                                <tr>
                                        <td class="one">
                                                <em>Skype:</em>
                                        </td>
                                        <td>
                                                <span><?=$profile->skype?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?if($profile->site != ''):?>
                                <tr>
                                        <td class="one">
                                            <em><?=X3::translate('Веб-сайт')?>:</em>
                                        </td>
                                        <td>
                                            <a href="/site/go/url/<?=base64_encode($profile->site)?>" target="_blank"><?=$profile->site?></a>
                                        </td>
                                </tr>
                                <?endif;?>
                                </table>
                            </div>
                            </div>
                        </td>
                    </tr>
                    <?endforeach;?>
                </table>
            </div>
        </div>
    </div>
    <div id="navi">
            <?=$paginator?>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>