<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PformFieldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pform Fields';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-field-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Pform Field', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'type',
            'value',
            'placeholder',
            // 'sort',
            'pform_uid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
