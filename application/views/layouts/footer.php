<table class="footer-cells">
    <tr>
        <td>
            <div class="footer-links">
                <nav>
                <?=X3_Widget::run('@layouts:menu.php',array('type'=>'Нижнее'))?>                    
                </nav>
            </div>
        </td>
        <?/*<td>
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
        </td>*/?>
        <td class="devby">
            <a href="http://www.zuber.kz" target="_blank"><img alt="Zuber" title="Сделано в Zuber" src="/images/devby.png" /></a>
        </td>
    </tr>
</table>
<div class="copyright"><?=SysSettings::getValue('Copyright','string','Копирайт','Общие','&copy; 2012 eKSK')?></div>