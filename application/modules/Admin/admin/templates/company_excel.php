<?php
$stat = X3_Session::readOnce('Company_Excel');
?>
<style>
    #x3-container table td {
        text-align: left;
    }
</style>
<div class="x3-submenu">
    <?=$modules->menu('excel');?>
</div>
<div class="x3-main-content">
    <div id="x3-header" x3-layout="header"><a href="/admin/<?= $action ?>"><?= $moduleTitle; ?></a> > Excel</div>
    <div id="x3-container" style="position:relative">
        <? if ($stat !== NULL): ?>
            <br />
            <hr/>
            <div><strong>Всего позиций:</strong> <?= $stat['all'] ?></div>
            <div><strong>Загружено:</strong> <?= $stat['inserted'] ?></div>
            <div><strong>Удалено:</strong> <?= $stat['deleted'] ?></div>
            <div><strong>Не найдено:</strong> <?= count($stat['not_found']) ?></div>
            <hr/>
            <?if(!empty($stat['not_found'])):?>
            <div style="position:absolute;right:10px;top:10px;width:300px;height:400px;background: #FFF;border: 3px solid #cccc00;
                 box-shadow: 0px 0px 15px #666666;padding:10px;">
                <a href="#X" onclick="$(this).parent().remove();" style="position: absolute;left:-12px;top:-12px;box-shadow: 0px 0px 15px #666666;
                   border-radius: 6px;background: #cccc00;width:15px;height:15px;display:block">
                    <img style="margin:2px 0 0 2px;display:block" src="/images/cross2.png" /></a>
                <div style="height:400px;overflow: auto;">
                <? $i=0;foreach ($stat['not_found'] as $found): ?>
                <div style="margin-bottom:10px;background:<?=($i%2)?'#fadada':'#fff'?>"><?=$found?></div>
                <?$i++; endforeach; ?>
                </div>
            </div>
            <?endif;?>
        <? endif; ?>
        <table width="100%" cellspacing="0">
            <tbody><tr valign="top">
                    <td>
                        <b>Загрузить позиции из Excel:</b><br>
                        <form method="post" enctype="multipart/form-data">
                            <input type="file" name="price">
                            <input type="submit" value=" Загрузить " name="load_price">
                            <hr/>
                    <table style="width:365px">
                        <tr>
                            <td align="right">Столбец названия:</td>
                            <td><select name="prop[name]" style="width:120px"><option selected value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец цены:</td>
                            <td><select name="prop[price]" style="width:120px"><option value="0">A<option value="1" selected>B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right"><nobr>Столбец ID товара:</nobr></td>
                        <td>
                            <select name="prop[id]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option selected value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select>
                            <input type="checkbox" value="md5" name="md5" />MD5?
                        </td>
                        </tr>
                        <tr>
                            <td align="right">Столбец URL:</td>
                            <td><select name="prop[url]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3" selected>D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                        <tr>
                            <td align="right">Столбец производителя:</td>
                            <td><select name="prop[manufacturer]" style="width:120px"><option value="-1">Игнорировать<option value="0">A<option value="1">B<option value="2">C<option value="3">D<option value="4">E<option value="5">F<option value="6">G<option value="7">H<option value="8">I<option value="9">J<option value="10">K<option value="11">L<option value="12">M<option value="13">N<option value="14">O<option value="15">P<option value="16">Q<option value="17">R<option value="18">S<option value="19">T<option value="20">U<option value="21">V<option value="22">W<option value="23">X<option value="24">Y<option value="25">Z</select></td>
                        </tr>
                    </table>                           
                        </form><br>
                        <!--form method="post">
                            <input type="submit" value="Скачать прайс-лист" name="get_price">
                        </form-->


                        <br><p style="border-left:1px solid #333;padding:7px;background: #efedee;font:italic 12px Verdana, sans-serif;color:#666666">
                        Розничная цена равная нулю означает, что товар отсутствует и не отображается.<br><br>
                        Значение столбца А может быть равным названию товара или его частью.<br>
                        Например, для товара в «<b>ASUS P6T6 WS Revolution</b>» значение столбца А в Excel'е <br>
                        может быть «<b>P6T6 WS Revolution</b>», но не «<b>ASUS</b>», т.к. «<b>ASUS</b>» не уникально.<br>
                        Если программа находит совпадение значения из Excel'я и наименования товара, то она добавляет этот товар в базу с новыми ценами.<br>
                        Товары, которые были добавлены в компанию ранее, но с которыми при загрузке Excel'я не произошло совпадение имён, не изменяются и не удаляются.<br>
                        Удалить товар при помощи Excel'я можно только, если проставить розничную цену равную нулю.
                        </p><br><br>
                        <!--a onclick="return confirm('Удалить все товары компании Instinct?')" class="red_" href="/_admin/company/?price=188&amp;delete_all=188"><b>Удалить все товары компании Instinct</b></a><br><br-->

                    </td>
                </tr>
            </tbody></table>        
    </div>
</div>