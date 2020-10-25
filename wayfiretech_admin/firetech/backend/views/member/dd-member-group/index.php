<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:41:51
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:44:08
 */
 

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\DdMemberGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dd Member Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">

<div class="dd-member-group-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="panel panel-default">
        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'group_id',
            'item_name',
            'level',
            'create_time:datetime',
            // 'update_time:datetime',
            ['class' => 'common\components\ActionColumn'],
        ],
    ]); ?>


</div>
    </div>
</div>
</div>