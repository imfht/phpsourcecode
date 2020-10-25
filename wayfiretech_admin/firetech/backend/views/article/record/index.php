<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\addons\diandi_dingzuo\models\searchs\recordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<ul class="nav nav-tabs">
    <li>
        <?= Html::a('添加 Record', ['create'], ['class' => '']) ?>
    </li>
    <li class="active">
        <?= Html::a('Record管理', ['index'], ['class' => 'btn btn-primary']) ?>
    </li>
</ul>
<div class="firetech-main">

    <div class="record-index ">
                                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                <div class="panel panel-default">
            <div class="box-body table-responsive">
                                    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                                'id',
            'user_id',
            'create_time:datetime',
            'update_time:datetime',
            'merchant',

                    ['class' => 'common\components\ActionColumn'],
                    ],
                    ]); ?>
                
                
            </div>
        </div>
    </div>
</div>