<style type="text/css">
    .module-user-header {
        height:10px;
        font:9px Tahoma;
        color:#4d357d;
    }
    .module-user-header div {
        float:left;
        cursor:default;
    }
    .module-user-header div[x3-order] {
        cursor:pointer;
    }
    .module-menu-list{position: relative;clear:left;}
    .module-menu-list .list-item {
        height: 35px;
        clear: left;
        overflow: hidden;
    }
    .module-menu-list .list-item div{
        float:left;
    }
    .module-menu-list .list-item .status {
        width:55px;
        padding-top: 8px;
    }
    .module-menu-list .list-item .title {
        width:265px;
        padding-left:20px;
        font:20px Tahoma;
        color:#5c5c5c;
    }
    .module-menu-list .list-item .email {
        width:265px;
        padding-left:20px;
        font:20px Tahoma;
        color:#5c5c5c;
    }
    .module-menu-list .list-item .trig {
        width:20px;
        padding-top: 8px;
        text-align: center;
    }
    .module-menu-list .list-item .trig i{
        font-size:0px;
        cursor:pointer;
        border:5px solid transparent;
        border-left-color:#000;
    }
    .module-menu-list .list-item .trig i.active{
        border-left-color:transparent;
        border-top-color:#000;
    }
    .plaha {
        position:absolute;
        left:692px;
        top:0px;
        border: 1px solid #4d357d;
        border-radius: 10px;
        width:540px;
        height:390px;
        overflow: auto;
    }
    .plaha .item {
        float:left;
        padding:5px;
        cursor: pointer;
        border-radius: 5px;
        color:#000;
        margin-left:10px;
        width:107px;
        margin-top:4px;
        margin-bottom:4px;
        position: relative;
    }
    .plaha .head {
        padding: 5px; 
        border-bottom: 1px solid #4d357d;
    }
    .plaha .item:hover{
        background-color: #705B99;
        color:#FFF;
    }
    .plaha .item.checked{
        background-color: #4d357d;
        color:#FFF;
    }
    .plaha .item .photo {
        background-position: 50% 50%;
        background-repeat: no-repeat;
        width:97px;
        height:97px;
        margin:0 auto;
    }
    .plaha .item .title {
        font-size: 12px;
        height: 14px;
        overflow: hidden;
        text-align: center;
    }
</style>
<div class="module-user-header">
    <div style="width:36px;padding-left:4px"><input type="checkbox" onclick="$('.submail').attr('checked',$(this).is(':checked'))" /></div>
    <div style="width:285px;" x3-order="title">Имя</div>
    <div style="width:285px;" x3-order="email">E-mail</div>
    <div style="">Опции</div>
</div>
<div class="module-menu-list">
    <?php foreach ($models as $i=>$model): ?>
    <div class="list-item" id="subscribe-item-<?=$model->id?>">
        <div class="trig"><input type="checkbox" class="submail" value="<?=$model->id?>" /></div>
        <div class="title" x3-order="title"><?php echo $model->name?></div>
        <div class="email"><?php echo $model->email?></div>
        <div class="options">
            <a href="#edit" onclick="call_edit(<?=$model->id?>,'<?=$class?>');return false;"><img alt="edit" src="/images/admin/edit.gif" title="Переместить в другую группу" /></a>
            <a href="#delete" onclick="_deletelist('<?=$model->id?>','<?=$class?>')"><img src="/images/admin/delete.gif" alt="delete" title="Удалить" /></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?
$ids = Syssettings::getValue('SubscribeStack', 'string[255]', 'ID товаров на рассылку', 'Рассылка', '');
$ids = explode(',', $ids);
$iss = Catalog::getInstance()->table->where('issend=0 AND status')->order('created_at DESC')->asArray();
if(!empty($iss)):
?>
<div class="plaha">
    <div class="head">
        <a href="#" style="float:right" onclick="save_stack(this);return false;">Сохранить</a>
        Товары для рассылки (<a href="#" onclick="$('.plaha .item').addClass('checked');return false;">все</a>)
    </div>
    <? foreach ($iss as $item): 
        $label = '&nbsp;';
        switch($item['is_special']){
            case 1:
                $label = '<div class="new">&nbsp;</div>';
            break;                                
            case 2:
                $label = '<div class="hit">&nbsp;</div>';
            break;
            case 3:
                $label = '<div class="star"><i style="margin-top:30px;">Акция</i></div>';
            break;
            case 4:
                $label = '<div class="star"><i>Супер цена</i></div>';
            break;
        }        
        ?>
    <div class="item<?=(in_array($item['id'], $ids))?' checked':''?>" iid="<?=$item['id']?>">
        <div title="<?=$item['title']?>" class="photo" style="background-image: url(/uploads/Catalog/97x97xh/<?=$item['image']?>)"><?=$label?></div>
        <div class="title"><?=$item['title']?></div>
    </div>
    <? endforeach; ?>
    <div class="clear-both">&nbsp;</div>
</div>
<?endif;?>
<script type="text/javascript">
    $('.trig i').click(function(){
        $('[parent-id="'+$(this).parent().parent().attr('id')+'"]').slideToggle();
        $(this).toggleClass('active')
    })
    $('.plaha .item').click(function(){
        $(this).toggleClass('checked');
    })
    function save_stack(el){
        var ids = [];
        $('.plaha .item.checked').each(function(){
            ids.push($(this).attr('iid'));
        });
        $.post('/admin/savestack',{'stack':ids.join(',')},function(m){$(el).html('OK!')})
    }
</script>