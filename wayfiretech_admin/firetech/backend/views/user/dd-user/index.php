<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DdUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">

<ul class="nav nav-tabs">
    <li>
        <?= Html::a('添加会员', ['create'], ['class' => '']) ?>
    </li>
    <li  class="active">
        <?= Html::a('会员管理', ['index'], ['class' => 'btn btn-primary']) ?>
    </li>
</ul>
<div class="firetech-main">

<div class="dd-user-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
    <div class="panel panel-default">
        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
            'open_id',
            'nickName',
            'avatarUrl',
            'gender',
            //'country',
            //'province',
            //'city',
            //'address_id',
            //'wxapp_id',
            //'create_time:datetime',
            //'update_time:datetime',

            ['class' => 'common\components\ActionColumn'],
        ],
    ]); ?>


</div>
    </div>
</div>
</div>
