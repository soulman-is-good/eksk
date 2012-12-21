<table class="footer-cells">
    <tr>
        <td width="270">
            <h2 class="callus">
                <?=X3::translate('Звоните нам');?>
                <span><?=SysSettings::getValue('CallUs','string','Звоните нам','Контакты','+7 (727) 123-45-67')?></span>
            </h2>
        </td>
        <td>
            <div class="footer-links">
                <?if(X3::app()->module->controller->id=='site' && X3::app()->module->controller->action=='index'):?>
                <span><?=X3::translate('На главную');?></span>
                <?else:?>
                <a href="/" title="<?=X3::translate('На главную');?>"><?=X3::translate('На главную');?></a>
                <?endif;?>
                <?if(X3::app()->module->controller->id=='site' && X3::app()->module->controller->action=='map'):?>
                <span><?=X3::translate('Карта сайта');?></span>
                <?else:?>
                <a href="/sitemap.html" title="<?=X3::translate('Карта сайта');?>"><?=X3::translate('Карта сайта');?></a>
                <?endif;?>
            </div>
        </td>
        <td>
            <div class="subscribe">
                <?if(Subscribe::isSent()):?>
                <div class="footer-links">
                    <span><?=X3::translate('Вы уже подписаны на рассылку новостей.');?></span>
                </div>
                <?else:?>
                <form action="/subscribe.html" method="post">
                    <span class="wrapper"><input type="text" name="email" placeholder="E-mail" />
                    <button type="submit" title="<?=X3::translate('Подписаться');?>">&nbsp;</button></span>
                </form>
                <?endif;?>
                <?if($ismain):?>
                <div style="margin-top:15px;" class="share42init"></div>
                <script type="text/javascript" src="<?=X3::app()->baseUrl?>/share42/share42.js"></script>
                <?endif;?>
            </div>            
        </td>
        <td>
            <div class="footer-links langs">
                <?if(X3::user()->lang=='kz'):?>
                <span>казакша</span>
                <?else:?>
                <a href="?lang=kz">казакша</a>
                <?endif;?>
                <span>|</span>
                <?if(X3::user()->lang=='ru'):?>
                <span>русский</span>
                <?else:?>
                <a href="?lang=ru">русский</a>
                <?endif;?>
                <span>|</span>
                <?if(X3::user()->lang=='en'):?>
                <span>english</span>
                <?else:?>
                <a href="?lang=en">english</a>
                <?endif;?>
            </div>
        </td>
        <td>
            <div class="search-bar">
                <span class="wrapper">
                    <form method="get" action="/search.html">
                    <input type="text" name="q" value="<?=X3::user()->search?>" placeholder="<?=X3::translate('Поиск по сайту');?>" />
                    <button type="submit"></button>
                    </form>
                </span>
            </div>
        </td>
    </tr>
</table>
<div class="copyright"><?=SysSettings::getValue('Copyright','string','Копирайт','Общие','&copy; АО "MAG" 2011')?></div>
<?if(NULL!=($msg=X3_Session::readOnce('subscribe'))):?>
<script>
    $(function(){
        $.alert('<?=addslashes($msg)?>','<?=X3::translate('Подписаться');?>');
    })
</script>
<? endif; ?>
