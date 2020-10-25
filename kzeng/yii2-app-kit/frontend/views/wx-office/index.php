<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\WxOffice;


use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\WxOfficeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wx Offices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-office-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wx Office', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
        echo Select2::widget([
        'name' => 'kv-state-200',
        'data' => WxOffice::getWxOfficeOption(),
        'size' => Select2::LARGE,
        'options' => ['placeholder' => 'Select a office ...'],
        'pluginOptions' => [
        'allowClear' => true
        ],
        ]);
    ?>

    <br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'office_id',
            [
                'attribute' => 'office_id',
                'headerOptions' => array('style'=>'width:5%;'),
            ],

            //'gh_id',
            //'scene_id',

            [
                'attribute' => 'title',
                'value'=>function ($model, $key, $index, $column) {
                    return WxOffice::getWxOfficeOption($model->title);
                },
                'filter'=> WxOffice::getWxOfficeOption(),
                'headerOptions' => array('style'=>'width:20%;'),
            ],


            //'branch',
            [
                'attribute' => 'branch',
                'headerOptions' => array('style'=>'width:10%;'),
            ],

            // 'region',
             'address',
            // 'manager',
            // 'member_cnt',
             //'mobile',

            [
                'attribute' => 'mobile',
                'headerOptions' => array('style'=>'width:10%;'),
            ],
            // 'pswd',
            // 'lat',
            // 'lon',
            // 'lat_bd09',
            // 'lon_bd09',
            // 'visable',
            // 'is_jingxiaoshang',
            // 'role',
            // 'status',
             'is_selfOperated',
            // 'score',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
