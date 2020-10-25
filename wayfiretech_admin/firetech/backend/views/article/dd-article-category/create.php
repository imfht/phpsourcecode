<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:38:58
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:16:07
 */
 

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticleCategory */

$this->title = '添加分类';
$this->params['breadcrumbs'][] = ['label' => '文章分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-article-category-create">

                <?= $this->render('_form', [
                    'model' => $model,
                    'catedata' => $catedata,
                    
                ]) ?>

            </div>
        </div>
    </div>
</div>