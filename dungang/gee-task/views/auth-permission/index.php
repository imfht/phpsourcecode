<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\widgets\BatchDeleteButton;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthPermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-permission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加', ['create'], [
            'class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#modal-dailog'
        ]) ?>

        <?= Html::a('批量添加', ['batch-create'], [
            'data-modal-size' => 'modal-lg',
            'class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#modal-dailog'
        ]) ?>

        <?= BatchDeleteButton::widget([
            'route' => ['batch-delete']
        ]) ?>

        <?= Html::a('下载模板', ['download-template'], [
            'data-pjax' => '0',
            'class' => 'btn btn-warning',
        ]) ?>
        
        <?= Html::a('导入', ['excel-import'], [
            'class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#modal-dailog'
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'name' => 'id'],
            'description:ntext',
            'name',
            'parent',
            'rule_name',
            [
                'class' => 'app\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttonsOptions' => [
                    'update' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-dailog'
                    ]
                ]
            ]
        ],
    ]); ?>
</div>