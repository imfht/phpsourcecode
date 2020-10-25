<?php

use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->title = '#' . $tag->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index">
    <div class="body-content">
        <h2><?= $this->title ?></h2>

        <?php /* @var $post yeesoft\post\models\Post */ ?>
        <?php foreach ($posts as $post) : ?>
            <?= $this->render('/items/post.php', ['post' => $post, 'page' => 'tag']) ?>
        <?php endforeach; ?>

        <div class="text-center">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
    </div>
</div>
