<div class="eksk-wnd">
    <div class="head">
        <em>
            <?if($user->isOnline()):?>
            <?=X3::translate('Онлайн');?>
            <?else:?>
            <?=X3::translate('Оффлайн');?>
            <?endif;?>
        </em>
        <h1><?=$user->name?> <?=$user->surname?></h1>
    </div>
    <div class="content">
        <div class="avaplace">
            <img src="/images/default.png" alt="" />
            <?if($user->id == X3::user()->id):?>
            <a href="/user/edit.html">Редактировать профиль</a>
            <?endif;?>
        </div>
        <div class="main_info">
            <table>
                <tbody><tr>
                        <td class="one">
                                <em><?=X3::translate('О себе')?>:</em>
                        </td>
                        <td>
                                <span>Скромный</span>
                        </td>
                </tr>
                <tr>
                        <td class="one">
                                <em><?=X3::translate('Пол');?>:	</em>
                        </td>
                        <td>
                                <span><?=X3::translate($user->gender)?></span>
                        </td>
                </tr>
                <tr>
                        <td class="without" colspan="2">
                                <h4>Контактная информация:</h4>
                        </td>
                </tr>
                <tr>
                        <td class="one">
                                <em>Телефон:</em>
                        </td>
                        <td>
                                <span>+7 707 123-45-67</span>
                        </td>
                </tr>
                <tr>
                        <td class="one">
                                <em>Skype:</em>
                        </td>
                        <td>
                                <span>konstantin</span>
                        </td>
                </tr>
                <tr>
                        <td class="one">
                                <em>Веб-сайт:</em>
                        </td>
                        <td>
                                <a href="#">http://zuber.kz</a>
                        </td>
                </tr>
                <tr class="no-border">
                        <td class="one">
                                <em>Адрес 1:</em>
                        </td>
                        <td>
                                <span>Алматы, проспект Абая, 134, 2 подъезд, квартира 34</span>
                                <a class="map_link" href="#"><span>Показать на карте</span><i>&nbsp;</i></a>

                        </td>
                </tr>
                <tr>
                        <td class="map" colspan="2">
                                <div class="map_image" style="display:none;">
                                        <img width="482" height="350" src="./uploads/map.jpg">
                                        <a href="#">Увеличить карту</a>
                                </div>
                        </td>
                </tr>
        </tbody></table>            
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="shadow"><i></i><b></b><em></em></div>
</div>
<script>
    $(function(){
        $('.map_link').click(function(){
            $(this).toggleClass('active');
            $(this).parent().parent().parent().find('.map_image').slideToggle();
            return false;
        })
    })
</script>