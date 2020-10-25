<?php include 'public_head.php'; ?>
<div class="article-category">
    <div class="title">
        相关标签
    </div>
    <div class="list-tag">
        <ul class="cl">
            <?php foreach ($list as $k => $v) : ?>
            <li>
                <a class="btn-link" href="<?= $v['url'] ?>"><?= $v['title'] ?><small>[<?= $v['total'] ?>]</small></a>
            </li>
            <?php endforeach; ?>
        </ul>  
    </div>
</div>
<?php include 'public_foot.php'; ?>