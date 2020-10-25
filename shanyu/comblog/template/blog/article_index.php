<?php include 'public_head.php'; ?>

<div class="nav-crumb">
    <a href="/">博客首页</a>
    <?php foreach ($crumb as $v): ?>
    / <a href="<?= $v['url'] ?>"><?= $v['title'] ?></a>
    <?php endforeach ?>
</div>

<?php include '_article_list.php'; ?>

<?php include 'public_foot.php'; ?>