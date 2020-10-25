<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:40:22
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 11:39:15
 */
 

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\DdMemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">

<div class="dd-member-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="panel panel-default">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>"{items}\n{pager}",
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'member_id',
                    'username',
                    'mobile',
                    'nickName',
                    // 'gender',
                    //'country',
                    //'province',
                    //'city',
                    //'address_id',
                    //'wxapp_id',
                    'create_time:datetime',
                    //'update_time:datetime',

                    ['class' => 'common\components\ActionColumn'],
                ],
            ]); ?>


</div>
    </div>
</div>
</div>