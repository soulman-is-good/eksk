<?php
$models = Menu::get(array('@condition'=>array('status','parent_id'=>NULL),'@order'=>'weight, title'));
?>
<div class="content sitemap">
    <div class="left_part">
        &nbsp;
    </div>
    <div class="right_part">
        <h1><?=X3::translate('Карта сайта');?></h1>
        <ul class="l1">
        <?foreach ($models as $model):
            $submenus = Menu::get(array('@condition'=>array('status','parent_id'=>$model->id),'@order'=>'weight, title'));
            $count = $submenus->count();
            ?>
            <li><a href="<?=$model->link?>"><?=$model->title?></a>
                <?if($count>0):?>
                <ul class="l2">
                    <?foreach($submenus as $sm):?>
                        <li><a href="<?=$sm->link?>"><?=$sm->title?></a>
                    <?endforeach;?>
                </ul>
                <?endif;?>
            </li>
        <?endforeach;?>
        </ul>
        </ul>
    </div>
</div>
    
