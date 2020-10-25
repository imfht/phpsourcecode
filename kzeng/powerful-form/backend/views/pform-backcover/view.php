<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PformBackcover */

$this->title = $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-backcover-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '删除，确定?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            //'content:ntext',
            [
                'label' => '内容',
                'attribute' => 'content',
                'format'=> 'html',
            ],
            
            'pform_uid',
        ],
    ]) ?>

</div>
