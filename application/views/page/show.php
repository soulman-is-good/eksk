<div class="eksk-wnd<?=(!X3::user()->isGuest()?'':' login')?>">
    <div class="head">
        <h1<?=(!X3::user()->isGuest()?'':' class="center"')?>><?=$model->title?></h1>
    </div>
    <div class="content"><?=$model->text?></div>
    <div class="shadow">&nbsp;</div>
</div>