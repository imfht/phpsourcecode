<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $post yeesoft\post\models\Post */

$page = (isset($page)) ? $page : 'post';
?>

<div class="post clearfix">
    <h2><?= Html::a($post->title, ["/site/{$post->slug}"]) ?></h2>

    <p class="text-justify">
        <?= $post->getThumbnail(['class' => 'thumbnail pull-left', 'style' => 'width: 160px; margin: 0 7px 7px 0']) ?>
        <?= ($page === 'post') ? $post->content : $post->shortContent ?>
    </p>

    <div class="clearfix" style="margin-bottom: 10px;">
        <div class="pull-left">
            <?php if ($post->category): ?>
                <b><?= Yii::t('yee/post', 'Posted in') ?></b>
                <a href="<?= Url::to(['/category/index', 'slug' => $post->category->slug]) ?>">"<?= $post->category->title ?>"</a>
            <?php endif; ?>
        </div>
        <div class="pull-right">
            <?php $tags = $post->tags; ?>
            <?php if (!empty($tags)): ?>
                <b><?= Yii::t('yee/post', 'Tags') ?>:</b>
                <?php foreach ($tags as $tag): ?>
                    <?= Html::a('#' . $tag->title, ['/tag/index', 'slug' => $tag->slug], ['class' => 'label label-primary']) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <span class="pull-right"><?= Yii::t('yee', 'Published') ?> by <b><?= $post->author->username ?></b> on <b><?= $post->publishedDate ?></b></span>

</div>

