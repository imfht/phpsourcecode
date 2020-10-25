<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\addons\diandi_dingzuo\models\record */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<ul class="nav nav-tabs">
    <li>
        <?= Html::a('添加 Record', ['create'], ['class' => '']) ?>
    </li>
    <li>
        <?= Html::a('Record管理', ['index'], ['class' => '']) ?>
    </li>
    <li class="active">
        <?= Html::a('Record管理', ['view'], ['class' => '']) ?>
    </li>
</ul>
<div class=" firetech-main">
    <div class="record-view">

        <div class="panel panel-default">
            <div class="box-body">

                <p>
                    <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
                            'id',
            'user_id',
            'create_time:datetime',
            'update_time:datetime',
            'merchant',
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>