<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-29 00:27:01
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:13:01
 */


use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdWebsiteSlide */

$this->title = '更新幻灯片' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '@SWG\Post(path="/v1', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-website-slide-update">


                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>