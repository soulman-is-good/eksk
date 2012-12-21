<?php
$lang = X3::user()->lang;
X3::user()->lang = 'ru';
?>
<html>
 <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Рассылка</title>
</head>
<body style="margin:0;padding:0">
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td width="79">
				<a href="/"><img alt="MAG" height="79" src="/images/logo.png" title="MAG" width="79" /></a></td>
			<td>
				<font color="#075A2C" size="5">Рассылка новостей за <?=I18n::date(time())?></font></td>
		</tr>
		<tr>
			<td colspan="2">
                            <?foreach($models as $model):?>
				<p>
					<font color="#555" size="1"><?=date('d.m.Y',$model->created_at)?></font><br />
					<a href="/news/<?=$model->id?>.html" style="text-decoration:none;border-bottom:1px solid #b3cdbf;"><font color="#075A2C" size="2"><?=$model->title?></font></a></p>
                                <?endforeach;?>
			</td>
                </tr>
                <tr>
			<td colspan="2">
                            &nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<font color="#cccccc" size="1"><?=SysSettings::getValue('Copyright','string','Копирайт','Общие','&copy; АО "MAG" 2011')?></font>
                        </td>
                        <td align="right">
                            <a href="/unsubscribe/<?=addslashes($unkey)?>.html" style="text-decoration:none"><font color="#075A2C" size="2"><b>ОТПИСАТЬСЯ</b></font></a>
                        </td>
		</tr>
	</tbody>
</table>
</body>
</html>
<?X3::user()->lang = $lang;?>