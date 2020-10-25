<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:38:20
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:15:30
 */
 

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticle */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?= $this->render('_tab') ?>

<div class=" firetech-main">
<div class="dd-article-view">

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
            'ishot',
            'pcate',
            'ccate',
            'template',
            'title',
            'description',
            'content:ntext',
            'thumb',
            'incontent',
            'source',
            'author',
            'displayorder',
            'linkurl',
            'createtime:datetime',
            'edittime:datetime',
            'click',
            'type',
            'credit',
        ],
    ]) ?>

</div>
    </div>
</div>
</div>