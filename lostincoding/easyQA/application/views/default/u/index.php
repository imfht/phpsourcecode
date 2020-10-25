<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <ul class="jie-row">
                    <?php if (is_array($article_lists)): ?>
                        <?php foreach ($article_lists as $_article): ?>
                            <li>
                                <?php if ($_article['is_top'] == 2): ?>
                                    <span class="fly-tip-stick">置顶</span>
                                <?php endif;?>
                                <?php if ($_article['is_fine'] == 2): ?>
                                    <span class="fly-jing">精</span>
                                <?php endif;?>
                                <a class="jie-title" href="/q/detail/<?=$_article['id']?>" target="_blank"><?=xss_filter($_article['article_title'])?></a>
                                <i><?=$_article['add_time']?></i>
                                <em><?=$_article['comment_counts']?>评/<?=$_article['view_counts']?>阅</em>
                            </li>
                        <?php endforeach;?>
                    <?php else: ?>
                        <li class="fly-none" style="min-height: 50px; padding:30px 0; height:auto;"><div style="font-size: 14px;">没有啦</div></li>
                    <?php endif;?>
                </ul>
            </div>
            <?=$page_html?>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>
</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>