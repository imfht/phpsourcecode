<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\doc\models\DocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文档';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doc-index">

    <p>
        <?= Html::a('添加文档', ['create'], ['class' => 'btn btn-success', 'data-modal-size' => 'modal-lg', 'data-toggle' => 'modal', 'data-target' => '#modal-dailog']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model['title'], ['view', 'gid' => $model['id']], ['data-pjax' => '0']);
                }
            ],
            'created_at:date',
            [
                'class' => '\app\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'buttonsOptions' => [
                    'update' => [
                        'data-toggle' => 'modal',
                        'data-modal-size'=>'modal-lg',
                        'data-target' => '#modal-dailog',
                    ],
                ]
            ]
        ]
    ]); ?>
</div>