<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:37:35
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-13 08:46:17
 */
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\DdArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_tab'); ?>

<div class="firetech-main">

<div class="dd-article-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
    <div class="panel panel-default">
        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cate.title',
            // 'ishot',
            // 'pcate',
            // 'ccate',
            // 'template',
            'title',
            //'description',
            //'content:ntext',
            //'thumb',
            //'incontent',
            //'source',
            //'author',
            //'displayorder',
            //'linkurl',
            //'createtime:datetime',
            //'edittime:datetime',
            //'click',
            //'type',
            //'credit',

            ['class' => 'common\components\ActionColumn'],
        ],
    ]); ?>


</div>
    </div>
</div>
</div>