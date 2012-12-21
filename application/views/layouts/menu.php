<?php
$menus = Menu::get(array('@condition'=>array('status','parent_id'=>NULL),'@order'=>'weight, title'));
X3::app()->menuCount = $menus->count();
?>
<ul>
    <?foreach($menus as $menu):
        $submenus = Menu::get(array('@condition'=>array('status','parent_id'=>$menu->id),'@order'=>'weight, title'));
        $count = $submenus->count();
    ?>
    <li>
        <a href="<?=$menu->link?>" <?if($count>0):?>class="sub"<?endif;?>><?=$menu->title?></a>
        <?if($count>0):?>
        <div class="submenu">
            <b><!--bridge--></b>
            <?foreach($submenus as $sm):?>
            <div><a href="<?=$sm->link?>"><?=$sm->title?></a></div>
            <?endforeach;?>
        </div>
        <?endif;?>
    </li>
    <?endforeach;?>
</ul>