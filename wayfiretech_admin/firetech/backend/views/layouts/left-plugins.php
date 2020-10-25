<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-01 01:26:15
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-08 17:28:30
 */


use yii\helpers\Html;
use yii\helpers\Url;
use diandi\admin\components\MenuHelper;
use richardfan\widget\JSRegister;

$menus = Yii::$app->params['menu'];
?>
<style>
    #addons-title {
        background: #2c3e50;
        padding: 10px;
        color: #fff;
        font-weight: bold;
        font-size: larger;
    }
</style>

<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
    <div class="box box-solid p-xs rfAddonMenu" id="accordion" style="padding-top: 30px;">
        <?php foreach ($menus as $key => $value) : ?>
            <div class="box-header with-border" style="cursor:pointer;padding: 10px 0 10px 0;">
                <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                    <div class="rf-box-title" data-toggle="collapse" data-parent="#accordion" href="#plugins-<?= $key ?>" style="font-size:16px;">
                        <?= $value['text'] ?>
                    </div>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    <i class="fa fa-fw fa-angle-down"></i>
                </div>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked" id="plugins-<?= $key ?>">
                    <?php if (!empty($value['children'])) : ?>
                        <?php foreach ($value['children'] as $k => $val) : ?>
                            <li class="border-bottom-none">
                                <a href="/backend/<?= $val['url'] ?>" title="应用入口">
                                    <i class="<?= $val['icon'] ?>"></i><?= $val['text'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php JSRegister::begin([
    'id' => 'plugins'
]) ?>
<script>
    $('#plugins-').on('show.bs.collapse', function() {
        // 执行一些动作...
    })
    $('#identifier').on('hidden.bs.collapse', function() {
        // 执行一些动作...
    })
</script>
<?php JSRegister::end(); ?>