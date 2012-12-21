<?php
$menus = Admin_Menu::get(array('@condition'=>array('status','role'=>array('LIKE'=>"'%".X3::user()->role."%'")),'@order'=>'weight'));
foreach ($menus as $menu) :
?>
<a href="<?=$menu->link?>"><?=$menu->title?></a>&nbsp;<span style="font-weight: bold">::</span>&nbsp;
<?endforeach;?>
<a style="float:right" href="/user/logout">Выход</a>
<br style="clear:right" />