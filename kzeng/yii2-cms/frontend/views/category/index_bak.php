<?php

use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->title = $category->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">
    <div class="body-content">
        <h2><?= $category->title ?></h2>

        <?php /* @var $post yeesoft\post\models\Post */ ?>
        <?php foreach ($posts as $post) : ?>
            <?= $this->render('/items/post.php', ['post' => $post, 'page' => 'category']) ?>
        <?php endforeach; ?>

        <div class="text-center">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
    </div>
</div>
