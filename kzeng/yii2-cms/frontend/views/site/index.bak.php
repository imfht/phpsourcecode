<?php

use yii\widgets\LinkPager;

/* @var $this yii\web\View */

$this->title = 'Homepage';
?>
<div class="site-index">

    <?php if (Yii::$app->getRequest()->getQueryParam('page') <= 1) : ?>
        <div class="jumbotron">
            <h1>Congratulations!</h1>

            <p class="lead">You have successfully created your Yii-powered application.</p>

            <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
        </div>
    <?php endif; ?>

    <div class="body-content">

        <?php /* @var $post yeesoft\post\models\Post */ ?>
        <?php foreach ($posts as $post) : ?>
            <?= $this->render('/items/post.php', ['post' => $post, 'page' => 'index']) ?>
        <?php endforeach; ?>

        <div class="text-center">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>

    </div>
</div>
