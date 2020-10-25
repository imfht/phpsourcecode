<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:40:25
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-04-12 18:40:35
 */
 

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticleCategory */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<ul class="nav nav-tabs">
   
    <li>
        <?= Html::a('分类管理', ['index'], ['class' => '']) ?>
    </li>
    <li>
        <?= Html::a('添加分类', ['create'], ['class' => '']) ?>
    </li>
    <li  class="active">
        <?= Html::a('分类管理', ['view'], ['class' => '']) ?>
    </li>
</ul>
<div class=" firetech-main">
<div class="dd-article-category-view">

    <div class="panel panel-default">
        <div class="box-body">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'displayorder',
            'type',
        ],
    ]) ?>

</div>
    </div>
</div>
</div>