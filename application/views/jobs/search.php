<div class="content jobs">
    <div class="left_part">
        <?=X3_Widget::run("@views:_widgets:jobs.php",array('inner'=>true));?>
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Поиск вакансий');?></h1>
        <form method="post" action="/search/jobs.html">
            <div class="row">
                <label for="q_keywords"><?=X3::translate('Ключевые слова');?></label>
                <input type="text" value="<?=isset($_POST['q'])?$_POST['q']['keywords']:''?>" name="q[keywords]" id="q_keywords" />
            </div>
            <div class="row">
                <label for="q_city"><?=X3::translate('Город');?></label>
                <select fcselect name="q[city]" id="q_city">
                <?if(is_resource($cities)) while($city = mysql_fetch_assoc($cities)):?>
                    <option value="<?=$city['id']?>"><?=$city['title']?></option>
                <?endwhile;?>
                </select>
            </div>
            <div class="row">
                <label for="q_sphere"><?=X3::translate('Сфера деятельности');?></label>
                <select fcselect name="q[sphere]" id="q_sphere">
                <?if(is_resource($spheres)) while($city = mysql_fetch_assoc($spheres)):?>
                    <option value="<?=$city['id']?>"><?=$city['title']?></option>
                <?endwhile;?>
                </select>
            </div>
            <div class="row">
                <label for="q_title"><?=X3::translate('Должность');?></label>
                <select fcselect name="q[title]" id="q_title">
                    <option value="">&nbsp;</option>
                <?$y = explode(';',X3::translate('без опыта работы;от 1 года;от 2 лет'));$ages = array();if(is_resource($titles)) while($city = mysql_fetch_assoc($titles)):?>
                    <option value="<?=addslashes($city['title'])?>"><?=$city['title']?></option>
                    <?
                    if(!isset($ages[$city['age']])){
                        if($city['age'] == 0)
                            $t = $y[0];
                        if($city['age'] % 10 == 1 && $city['age'] != 11)
                            $t = str_replace("1",$city['age'],$y[1]);
                        else
                            $t = str_replace("2",$city['age'],$y[2]);
                        $ages[$city['age']] = '<option value="'.$city['age'].'">'.$t.'</option>';
                    }
                    ?>
                <?endwhile;ksort($ages);?>
                </select>
            </div>
            <div class="row">
                <label for="q_age"><?=X3::translate('Опыт работы');?></label>
                <select fcselect name="q[age]" id="q_age">
                    <?=implode("\r\n",$ages)?>
                </select>
            </div>
            <div class="row">
                <button style="margin-left: 160px;" type="submit"><i>&nbsp;</i><?=X3::translate('Искать');?><b>&nbsp;</b></button>
            </div>
        </form>
    </div>
</div>