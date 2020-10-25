<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:40:07
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:16:16
 */
 

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticleCategory */

$this->title = '修改分类: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章 Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-article-category-update">


                <?= $this->render('_form', [
                    'model' => $model,
                    'catedata' => $catedata,

                ]) ?>
            </div>
        </div>
    </div>
</div>
