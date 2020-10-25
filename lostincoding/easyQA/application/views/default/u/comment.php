<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content">
            <?php require_once VIEWPATH . "$theme_id/u/inc/nav.inc.php";?>
            <div class="user-mine">
                <ul class="home-jieda">
                    <?php if (is_array($comment_lists)): ?>
                        <?php foreach ($comment_lists as $_comment): ?>
                            <li>
                                <p>
                                    <span><?=time_tran($_comment['add_time'])?></span>
                                    在<a href="/q/detail/<?=$_comment['article_id']?>" target="_blank"><?=xss_filter($_comment['article_title'])?></a>中评论：
                                </p>
                                <div class="home-dacontent"><?=html_newline(content_xss_filter($_comment['comment_content']))?></div>
                            </li>
                        <?php endforeach;?>
                    <?php else: ?>
                        <li class="fly-none" style="min-height: 50px; padding:30px 0; height:auto;"><span>没有任何评论</span></li>
                    <?php endif;?>
                </ul>
            </div>
            <?=$page_html?>
        </div>
    </div>
    <?php require_once VIEWPATH . "$theme_id/u/inc/sidebar.inc.php";?>
</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>