<div class="am-g am-g-fixed blog-g-fixed">
    <div class="am-u-md-8">
        <?php foreach ($articleList as $article) { ?>
            <article class="blog-main">
                <h3 class="am-article-title blog-title">
                    <a href="/article/<?php echo $article['id']; ?>"><?php echo $article['name']; ?></a>
                </h3>
                <p class="am-article-meta blog-meta">
                    时间：<?php echo $article['datetime']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    栏目：<a href="/category/<?php echo $article['cat']; ?>"><?php echo $article['catname']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    标签：
                    <?php foreach ($article['tagarray'] as $tag) { ?>
                        <a href="<?php echo $tag['url']; ?>"><?php echo $tag['name']; ?></a>&nbsp;&nbsp;
                    <?php } ?>
                </p>
                <div class="am-g blog-content">
                    <?php if (!empty($article['thumbpic'])) { ?>
                        <div class="am-u-lg-12">
                            <p><img src="<?php echo $article['thumbpic']; ?>"></p>
                        </div>
                    <?php } ?>
                    <div class="am-u-lg-12">
                        <?php echo $article['description']; ?>
                    </div>
                </div>
            </article>
            <hr>
        <?php } ?>
        <ul class="am-pagination blog-pagination">
            <li class="am-pagination-prev"><a href="/category/<?php echo $cat.'/'.$uppage; ?>">&laquo; 上一页</a></li>
            <li class="am-pagination-next"><a href="/category/<?php echo $cat.'/'.$downpage; ?>">下一页 &raquo;</a></li>
        </ul>
    </div>
    <?php include 'sidebar.php'; ?>
</div>
