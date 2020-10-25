<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CustomerPformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Pforms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-pform-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer Pform', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pform_uid',
            'pform_field_id',
            'value',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
