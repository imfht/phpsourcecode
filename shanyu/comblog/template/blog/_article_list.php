<div class="article-list">
    <?php foreach ($articles['list'] as $k => $v): ?>
    <div class="article-item">
        <div class="title">
            <a href="<?= $v['url'] ?>" title="点击阅读"><?= $v['title'] ?></a>
        </div>
        <div class="info cl">
            <div class="fl">
                <span>
                    <a class="btn-link" href="<?= $v['category']['url'] ?>"><?= $v['category']['title'] ?></a>
                </span>
                <?php if(!empty($v['tags'])): ?>
                <span>
                    <?php foreach ($v['tags'] as $tag): ?>
                    <a class="btn-link" href="<?= $tag['url'] ?>"><?= $tag['title'] ?></a>
                    <?php endforeach ?>
                </span>
                <?php endif; ?>
            </div>
            <div class="fr">
                <span><?= $v['create_time'] ?>发布</span>
            </div>
        </div>
        <div class="desc cl"><?= $v['description'] ?></div>
    </div>
    <?php endforeach ?>
</div>

<div class="article-page">
    <?= $articles['page'] ?>
</div>