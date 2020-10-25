<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-14 08:13:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:14:12
 */
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\联系我们Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '联系我们s';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">

<div class="dd-website-contact-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
    <div class="panel panel-default">
        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            'name',
            'contact',
            'createtime:datetime',
            // 'updatetime:datetime',

            ['class' => 'common\components\ActionColumn'],
        ],
    ]); ?>


</div>
    </div>
</div>
</div>