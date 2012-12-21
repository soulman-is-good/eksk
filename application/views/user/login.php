<?php
$user = new User;
$form = new Form($user);
?>
<table class="eksk-login-form">
<?
echo $form->renderPartial(array('login','password'));
?>
</table>
