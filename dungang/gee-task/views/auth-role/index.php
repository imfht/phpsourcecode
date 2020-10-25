<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
        <?= Html::a('添加角色', ['create'], ['class' => 'btn btn-success','data-toggle'=>'modal','data-target'=>'#modal-dailog']) ?>
    </p>

    <?php

echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            'scope',
            'description',
            [
                'label' => '权限',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('授权', [
                        'permission',
                        'name' => $model['name']
                    ]);
                }
            ],

            [
                'class' => '\app\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'buttonsOptions' => [
                    'update' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-dailog'
                    ]
                ]
            ]
        ]
    ]);
    ?>
</div>
