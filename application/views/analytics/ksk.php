<div class="eksk-wnd">
    <div class="head">
        <h1><?=X3::translate('Аналитические данные');?></h1>
    </div>
    <div class="content">
        <div class="tabs" fctabs="ksk">
            <ul>
                <li><a href="#ksk"><?=X3::translate('КСК');?></a></li>
                <li><a href="/analytics/user.html"><?=X3::translate('Жильцы');?></a></li>
                <li><a href="/analytics/vote.html"><?=X3::translate('Опросы');?></a></li>
                <li><a href="/analytics.html"><?=X3::translate('Активность пользователей');?></a></li>
            </ul>
            <div class="tab" id="ksk">
                <div class="stats">
                    <b><?=X3::translate('КСК');?>: <?=$count?></b> <a href="/uploads/excel/generate/ksk.xls" class="excel"><span><?=X3::translate('Экспорт в Excel');?></span></a>
                </div>
                <table class="admin-list">
                    <?foreach($models as $model):
                        $addreses = User_Address::get(array('@condition'=>array('user_id'=>$model->id),'@order'=>'status, id ASC'));
                        $profile = User_Settings::get(array('user_id'=>$model->id),1);
                        $rank = User_Rank::add($model->id);
                        ?>
                    <tr>
                        <td class="ava"><img src="<?=$model->avatar?>" width="100" alt="" /></td>
                        <td class="name">
                            <div class="mb-5"><b><?=$model->ksksurname?> <?=$model->kskname?></b></div>
                            <div class="mb-5"><em><?=$model->duty?></em></div>
                            <a href="/user/<?=$model->id?>.html"><?=$model->fullname?><?=$model->status==0?'<em>'.X3::translate('Не активирован').'</em>':''?></a>
                            <div class="with_stars" style="margin-top:8px;">
                                <i><?=X3::translate('Рейтинг');?>:</i>
                                <div class="blank" data-width="<?=$rank?>">
                                    <div class="starz"></div>
                                    <div class="hollow"></div>
                                    <div class="full" style="width:<?=$rank?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="ops">
                            <div class="line">
                            <a class="dash" href="#main-info"><span><?=X3::translate('Основная информация')?></span>
                            </a>
                            <div class="popup">
                                <div class="info-row">
                                    <?=$model->name?>
                                </div>
                                <div class="info-row">
                                    <?=X3::translate('Основная информация')?>:
                                </div>
                                <table width="100%">
                                <?if($model->kskname != '' || $model->ksksurname != ''):?>
                                <tr>
                                    <td><em><?=$model->duty?>:</em></td>
                                    <td><?=$model->kskname?> <?=$model->ksksurname?></td>
                                </tr>
                                <tr>
                                    <td><em><?=X3::translate('Пол');?>:</em></td>
                                    <td><?=X3::translate($model->gender)?></td>
                                </tr>
                                <?endif;?>
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
                                    <?=$model->kskname?> <?=$model->ksksurname?> <em><?=$model->duty?></em>
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
                                <?if(!empty($addreses)) foreach($addreses as $i=>$address):?>
                                <?if($address):?>
                                <tr>
                                        <td class="one">
                                            <em><?=$address->status?X3::translate('Дом').$i:X3::translate('Адрес офиса')?>:</em>
                                        </td>
                                        <td>
                                            <span><?=$address->city->title?>, <?=$address->street->title?>, <?=$address->house?><?if($address->flat>0):?>, <?=X3::translate('офис')?> <?=$address->flat?><?endif;?></span>
                                        </td>
                                </tr>
                                <?endif;?>
                                <?endforeach;?>
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