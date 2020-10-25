<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DdUser */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => '会员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="box box-default">
<ul class="nav nav-tabs">
    <li>
        <?= Html::a('添加会员', ['create'], ['class' => '']) ?>
    </li>
    <li>
        <?= Html::a('会员管理', ['index'], ['class' => '']) ?>
    </li>
    <li  class="active">
        <?= Html::a('会员查看', ['view'], ['class' => '']) ?>
    </li>
</ul>
<div class=" firetech-main">
<div class="dd-user-view">

    <div class="panel panel-default">
        <div class="box-body">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->user_id], [
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
            'user_id',
            'open_id',
            'nickName',
            'avatarUrl',
            'gender',
            'country',
            'province',
            'city',
            'address_id',
            'wxapp_id',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
    </div>
</div>
</div>
</div>