<style type="text/css">
    .admin-tabs {
        list-style: none 0 none;
        margin: 0;
        padding:0;
        line-height: 0;
    }
    .admin-tabs li {
        display:inline-block;        
        margin: 19px 4px 17px 0;
        padding: 0;
    }
    .admin-tabs li a.active {
        background: #fff;
        color:#000;
    }
    .admin-tabs li a {
        padding:10px;
        border:1px solid #000;
        border-bottom: none;
        border-radius: 10px 0px 0 0;
        background: #4D357D;
        text-decoration: none;
        color: #fff;
        font-weight: bold;
    }
    .tab{
        border-top:1px solid #000000;
        background: #fff;       
    }
</style>
<?
    function allow($dir,$level=0,$mod=''){
        if(FALSE === ($h = opendir($dir)))
            return false;
        $allow = array();
        while($file = readdir($h)){
            if($file!='.' && $file!='..'){
                $ext = pathinfo($file,PATHINFO_EXTENSION);
                if(is_dir($dir . DIRECTORY_SEPARATOR . $file) && $level<1){
                    $allow += allow($dir . DIRECTORY_SEPARATOR . $file,$level+1,$file."_");
                }elseif($ext=='php'){
                    $module = $mod.pathinfo($file,PATHINFO_FILENAME);
                    if(!in_array($module,  SeoHelper::$deny) && $module instanceof X3_Module_Table){
                        echo $module." ||| ";
                        //I think if there is metatitle then there must be other fields we need
                        $m = X3_Module_Table::getInstance($module);
                        if(property_exists($m, '_fields'))
                        if(isset($m->_fields['metatitle'])){
                            $allow[] = $module;
                        }
                    }
                }
            }
        }
        return $allow;
    }
    $allow =SeoHelper::$allow;// allow(X3::app()->getPathFromAlias('@app:modules'));
?>
<div class="x3-main-content">
    
<div id="x3-header" x3-layout="header">SEO</div>

<div id="x3-buttons" x3-layout="buttons">
    <a id="save" href="#/admin/seo/save">Сохранить</a>
</div>

<div id="x3-container">
<ul class="admin-tabs">
    <li><a href="#" id="common" class="active">Общие</a></li>        
<?    foreach($allow as $a):?><li><a id="<?=strtolower($a)?>" href="#"><?=SeoHelper::$labels[$a]?></a></li><?endforeach;?>
</ul>
<?
    $fields = array('id','name','value');
    foreach(X3::app()->languages as $lang){
        $fields[]='value_'.$lang;
    }
    $fields = implode(', ', $fields);
    $titles = SysSettings::getInstance()->table->select($fields)->where("`name` LIKE 'SeoTitle%'")->asArray();
    $keywords = SysSettings::getInstance()->table->select($fields)->where("`name` LIKE 'SeoKeywords%'")->asArray();
    $descriptions = SysSettings::getInstance()->table->select($fields)->where("`name` LIKE 'SeoDescription%'")->asArray();
    $seo = array();
    foreach ($titles as $value) {
        $mod = substr($value['name'],8);
        if($mod == '') continue;
        $seo[$mod]['title'] = $value;
    }
    foreach ($keywords as $value) {
        $mod = substr($value['name'],11);
        if($mod == '') continue;
        $seo[$mod]['keywords'] = $value;
    }
    foreach ($descriptions as $value) {
        $mod = substr($value['name'],14);
        if($mod == '') continue;
        $seo[$mod]['description'] = $value;
    }
?>
<form action="/seo/save.html" method="post" id="seo-form">
<div class="tab" id="common-tab">
<?    
    foreach($seo as $mod=>$s):
?>    
    <div>
        <h3><?=isset(SeoHelper::$labels[$mod])?SeoHelper::$labels[$mod]:$mod;?></h3>
    <div>
        METATITLE:
            <input type="hidden" name="SysSettings[<?=$s['title']['id']?>][id]" value="<?=$s['title']['id']?>" />
            <input type="text" name="SysSettings[<?=$s['title']['id']?>][value]" value="<?=$s['title']['value']?>" size="45" />
        KEYWORDS:
            <input type="hidden" name="SysSettings[<?=$s['keywords']['id']?>][id]" value="<?=$s['keywords']['id']?>" />
            <input type="text" name="SysSettings[<?=$s['keywords']['id']?>][value]" value="<?=$s['keywords']['value']?>" size="45" />
        DESCRIPTION:
            <input type="hidden" name="SysSettings[<?=$s['description']['id']?>][id]" value="<?=$s['description']['id']?>" />
            <input type="text" name="SysSettings[<?=$s['description']['id']?>][value]" value="<?=$s['description']['value']?>" size="45" />
    </div>
<?foreach(X3::app()->languages as $lang):?>        
    <div>
        METATITLE_<?=$lang?>:
            <input type="text" name="SysSettings[<?=$s['title']['id']?>][value_<?=$lang?>]" value="<?=$s['title']['value_'.$lang]?>" size="45" />
        KEYWORDS_<?=$lang?>:
            <input type="text" name="SysSettings[<?=$s['keywords']['id']?>][value_<?=$lang?>]" value="<?=$s['keywords']['value_'.$lang]?>" size="45" />
        DESCRIPTION_<?=$lang?>:
            <input type="text" name="SysSettings[<?=$s['description']['id']?>][value_<?=$lang?>]" value="<?=$s['description']['value_'.$lang]?>" size="45" />
    </div>
<?endforeach;?>        
    </div>            
    <?endforeach;?>
</div>
<?
    $fields = array('id','title','metatitle','metakeywords','metadescription');
    foreach(X3::app()->languages as $lang){
        $fields[]='metatitle_'.$lang;
        $fields[]='metakeywords_'.$lang;
        $fields[]='metadescription_'.$lang;
    }
    $fields = implode(', ', $fields);
    foreach($allow as $a):        
    $models = X3_Module::getInstance($a)->table->select($fields)->where()->asArray();    
    if(!empty($models)):
?><div class="tab" id="<?=strtolower($a)?>-tab" style="display:none"><?        
    foreach($models as $model):
?>
    <h3><?=$model['title']?></h3>
    <div>
            <input type="hidden" name="<?=$a?>[<?=$model['id']?>][id]" value="<?=$model['id']?>" />
        METATITLE:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metatitle]" value="<?=$model['metatitle']?>" size="45" />
        KEYWORDS:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metakeywords]" value="<?=$model['metakeywords']?>" size="45" />
        DESCRIPTION:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metadescription]" value="<?=$model['metadescription']?>" size="45" />
    </div>
<?foreach(X3::app()->languages as $lang):?>        
    <div>            
        METATITLE_<?=$lang?>:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metatitle_<?=$lang?>]" value="<?=$model['metatitle_'.$lang]?>" size="45" />
        KEYWORDS_<?=$lang?>:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metakeywords_<?=$lang?>]" value="<?=$model['metakeywords_'.$lang]?>" size="45" />
        DESCRIPTION_<?=$lang?>:
            <input type="text" name="<?=$a?>[<?=$model['id']?>][metadescription_<?=$lang?>]" value="<?=$model['metadescription_'.$lang]?>" size="45" />
    </div>
<?endforeach;?>    
    <? endforeach;?></div><? endif;?>
<? endforeach; ?>
</form>
        <script>
            $('.admin-tabs li a').click(function(){
                var ac = $('.admin-tabs li a.active');
                $("#"+ac.attr('id')+"-tab").css('display','none');
                ac.removeClass('active');
                $(this).addClass('active');
                $("#"+$(this).attr('id')+"-tab").css('display','block');
            })
            $('#save').click(function(){  
                var data = $('#seo-form').serialize();
                 $.post('/seo/save.html',data,function(m){
                     if(m.status=='OK'){
                         $.growl('Сохранено','ok');
                     }else
                         $.growl(m.message);
                 },'json')            
            })
        </script>
</div>
</div>

