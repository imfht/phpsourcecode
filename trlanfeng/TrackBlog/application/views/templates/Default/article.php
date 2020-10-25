<div class="am-g am-g-fixed blog-g-fixed">
    <div class="am-u-md-8">
            <article class="blog-main">
                <h3 class="am-article-title blog-title">
                    <a href="/article/<?php echo $id; ?>"><?php echo $name; ?></a>
                </h3>
                <p class="am-article-meta blog-meta">
                    时间：<?php echo $datetime; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    栏目：<a href="/category/<?php echo $cat; ?>"><?php echo $catname; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                    标签：
                    <?php foreach ($tagarray as $tag) { ?>
                        <a href="/tag/<?php echo $tag; ?>"><?php echo $tag; ?></a>&nbsp;&nbsp;
                    <?php } ?>
                </p>
                <div class="am-g blog-content">
                    <?php if (!empty($thumbpic)) { ?>
                        <div class="am-u-lg-12">
                            <p><img src="<?php echo $thumbpic; ?>"></p>
                        </div>
                    <?php } ?>
                    <div class="am-u-lg-12">
                        <?php echo $content; ?>
                    </div>
                </div>
            </article>
            <hr>
        <ul class="am-pagination blog-pagination">
            <li class="am-pagination-prev"><a href="/category/<?php //echo $cat.'/'.$uppage; ?>">&laquo; 上一页</a></li>
            <li class="am-pagination-next"><a href="/category/<?php //echo $cat.'/'.$downpage; ?>">下一页 &raquo;</a></li>
        </ul>
    </div>
    <?php include 'sidebar.php'; ?>
</div>
