<?php
if($type == 'Нижнее'):
$menus = Menu::get(array('@condition'=>array('status','type'=>'Нижнее'),'@order'=>'weight, title'));
?>
    <a href="#">Пользовательское соглашение</a>&nbsp;
    <a href="#">Правила</a>&nbsp;
    <a href="#">Помощь</a>&nbsp;
    <?foreach($menus as $menu):?>
    <a href="<?=$menu->link?>"><?=$menu->title?></a>
    <?endforeach;?>
<?elseif(!X3::user()->isGuest()):?>
    <div class="left_menu">
        <a href="#notify" class="menu_item notify"><span><?=X3::translate('Мои оповещения');?></span></a>
        <?if(X3::app()->request->isActive('/')):?>
        <span class="menu_item profile"><span><?=X3::translate('Мой профиль');?></span></span>
        <?else:?>
        <a href="/" class="menu_item profile"><span><?=X3::translate('Мой профиль');?></span></a>
        <?endif;?>
        <a href="#ksk/" class="menu_item ksk"><span><?=X3::translate('КСК');?></span></a>
        <a href="#users/" class="menu_item users"><span><?=X3::translate('Жильцы');?></span></a>
        <?if(X3::app()->request->isActive('/admins')):?>
        <span class="menu_item admins"><span><?=X3::translate('Администраторы');?></span></span>
        <?else:?>
        <a href="/admins/" class="menu_item admins"><span><?=X3::translate('Администраторы');?></span></a>
        <?endif;?>
    </div>
<? endif; ?>
