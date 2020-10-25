<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:39:42
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:16:12
 */
use leandrogehlen\treegrid\TreeGrid;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\DdArticleCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">

<div class="dd-article-category-index ">
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>
    <div class="panel panel-default">
        <div class="box-body">
        <?= TreeGrid::widget([
                    'dataProvider' => $dataProvider,
                    'keyColumnName' => 'id',
                    'parentColumnName' => 'pcate',
                    'parentRootValue' => '0', //first parentId value
                    'pluginOptions' => [
                        'initialState' => 'collapsed',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'id',
                        'title',
                        'displayorder',
                        'type',

                        ['class' => 'common\components\ActionColumn'],
                    ],
                ]); ?>


</div>
    </div>
</div>
</div>