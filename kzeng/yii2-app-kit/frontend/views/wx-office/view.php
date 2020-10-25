<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\WxOffice */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Wx Offices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-office-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->office_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->office_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'office_id',
            'gh_id',
            'scene_id',
            'title',
            'branch',
            'region',
            'address',
            'manager',
            'member_cnt',
            'mobile',
            'pswd',
            'lat',
            'lon',
            'lat_bd09',
            'lon_bd09',
            'visable',
            'is_jingxiaoshang',
            'role',
            'status',
            'is_selfOperated',
            'score',
        ],
    ]) ?>

</div>
