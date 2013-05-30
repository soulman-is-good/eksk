<?php
if($type == 'Нижнее'):
$menus = Menu::get(array('@condition'=>array('status','type'=>'Нижнее'),'@order'=>'weight, title'));
?>
    <?foreach($menus as $menu):?>
    <a href="<?=$menu->link?>"><?=$menu->title?></a>
    <?endforeach;?>
<?elseif(!X3::user()->isGuest()):
    ?>
    <div class="left_menu">
        <?if(X3::app()->request->isActive('/warning')):?>
        <span class="menu_item notify"><span><?=X3::translate('Мои оповещения');?></span></span>
        <?else:?>
        <a href="/warning/" class="menu_item notify"><span><?=X3::translate('Мои оповещения');?></span></a>
        <?endif;?>
        <?if(X3::app()->request->isActive('/message')):?>
        <span class="menu_item message"><span><?=X3::translate('Мои сообщения');?></span></span>
        <?else:?>
        <a href="/message/" class="menu_item message"><span><?=X3::translate('Мои сообщения');?></span></a>
        <?endif;?>
        <?if(X3::app()->request->isActive('/')):?>
        <span class="menu_item profile"><span><?=X3::translate('Мой профиль');?></span></span>
        <?else:?>
        <a href="/" class="menu_item profile"><span><?=X3::translate('Мой профиль');?></span></a>
        <?endif;?>
        <?if(!X3::user()->isKsk()):?>
            <?if(X3::app()->request->isActive('/ksk')):?>
            <span class="menu_item ksk"><span><?=X3::translate('КСК');?></span></span>
            <?else:?>
            <a href="/ksk/" class="menu_item ksk"><span><?=X3::translate('КСК');?></span></a>
            <?endif;?>
        <?endif;?>
        <?if(X3::app()->request->isActive('/users')):?>
        <span class="menu_item users"><span><?=X3::translate('Жильцы');?></span></span>
        <?else:?>
        <a href="/users/" class="menu_item users"><span><?=X3::translate('Жильцы');?></span></a>
        <?endif;?>
        <?if(X3::app()->request->isActive('/forum')):?>
        <span class="menu_item themes"><span><?=X3::translate('Темы обсуждения');?></span></span>
        <?else:?>
        <a href="/forum/" class="menu_item themes"><span><?=X3::translate('Темы обсуждения');?></span></a>
        <?endif;?>
        <?if(X3::app()->request->isActive('/reports')):?>
        <span class="menu_item stats"><span><?=X3::translate('Отчеты');?></span></span>
        <?else:?>
        <a href="/reports/" class="menu_item stats"><span><?=X3::translate('Отчеты');?></span></a>
        <?endif;?>
        <?if(X3::app()->request->isActive('/vote')):?>
        <span class="menu_item questions"><span><?=X3::translate('Мои опросы');?></span></span>
        <?else:?>
        <a href="/vote/" class="menu_item questions"><span><?=X3::translate('Мои опросы');?></span></a>
        <?endif;?>
        <?if(X3::user()->isAdmin()):?>
            <?if(X3::app()->request->isActive('/admins')):?>
            <span class="menu_item admins"><span><?=X3::translate('Администраторы');?></span></span>
            <?else:?>
            <a href="/admins/" class="menu_item admins"><span><?=X3::translate('Администраторы');?></span></a>
            <?endif;?>
            <?if(X3::app()->request->isActive('/analytics')):?>
            <span class="menu_item analytics"><span><?=X3::translate('Аналитические данные');?></span></span>
            <?else:?>
            <a href="/analytics.html" class="menu_item analytics"><span><?=X3::translate('Аналитические данные');?></span></a>
            <?endif;?>
        <?endif;?>
    </div>
<? endif; ?>
