<?php include 'public_head.php'; ?>
<div class="nav-crumb">
    <a href="/">博客首页</a>
    <?php foreach ($crumb as $v): ?>
    / <a href="<?= $v['url'] ?>"><?= $v['title'] ?></a>
    <?php endforeach ?>
</div>

<div class="article-detail">
    <div class="title">
        <?= $detail['title'] ?>
    </div>
    <div class="info">
        <span>
            发布日期: <?= $detail['create_time'] ?>
        </span>
        <span>
            阅读总量: <?= $detail['view'] ?>
        </span>
    </div>
    <?php if(!empty($tags)): ?>
    <div class="tags">
        <?php foreach ($tags as $tag): ?>
        <a class="btn-link" href="<?= $tag['url'] ?>"><?= $tag['title'] ?></a>
        <?php endforeach ?>
    </div>
    <?php endif; ?>
    <div class="content">
        <?= $detail['content'] ?>
    </div>

    <!-- 网易云评论 -->
    <div id="cloud-tie-wrapper" class="cloud-tie-wrapper"></div>

</div>

<?php
$_scripts .= <<<str
<!-- 代码高亮 -->
<link rel="stylesheet" href="/assets/addons/highlight/highlight.min.css">
<script src="/assets/addons/highlight/highlight.min.js"></script>
<script>
$(document).ready(function() {
  $('pre').each(function(i, block) {
    hljs.highlightBlock(block);
  });
});
</script>
str;
?> 
<?php include 'public_foot.php'; ?>