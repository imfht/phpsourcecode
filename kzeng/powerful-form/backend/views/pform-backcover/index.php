<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PformBackcoverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '表单成功页面';
$this->params['breadcrumbs'][] = ['label' => '表单', 'url' => ['pform/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pform-backcover-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <!--
    <p>
        <//?= Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            //'content:ntext',
            'pform_uid',
        
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
