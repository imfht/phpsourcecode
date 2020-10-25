<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerPformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '客户表单数据';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-pform-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--
    <p>
        <//?= Html::a('Create Customer Pform', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    -->


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            //'pform_uid',
            [
                'attribute' => 'pform_uid',
                'label' => '表单编码',
            ], 

            [
                'label' => '表单名称',

                'format' => 'html',
                'value' => function($model, $key, $index, $column){
                    $pform = backend\models\Pform::findOne(['uid' => $model->pform_uid]);
                    
                    return empty($pform->title) ? '--' : $pform->title;

                },
                // 'headerOptions' => array('style'=>'width:160px;'),
            ],  

            //'pform_field_id',
            [
                'attribute' => 'pform_field_id',
                'label' => '字段名称',

                'format' => 'html',
                'value' => function($model, $key, $index, $column){
                    $pformfield = backend\models\PformField::findOne(['id' => $model->pform_field_id]);
                    
                    return empty($pformfield->title) ? '--' : $pformfield->title;

                },
                // 'headerOptions' => array('style'=>'width:160px;'),
            ],  

            'value',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                //'headerOptions' => array('style'=>'width:8%;'),
            ],

        ],
    ]); ?>
</div>
