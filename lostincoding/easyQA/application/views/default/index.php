<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<?php if (!empty($user['email']) && $user['email_status'] == 1): ?>
    <div style="max-width: 1210px; margin: 0 auto 10px; padding: 0 5px; color: #f00;">
        <p>
            <span>您绑定的邮箱<?=$user['email']?>还未激活，请进入您的邮箱激活，如果没有收到邮件，请</span>
            <a href="javascript:;" onclick="resend_bind_email();">重发验证邮件</a>
        </p>
    </div>
<?php endif;?>
<div id="main" class="main layui-clear">
    <?php require_once VIEWPATH . "$theme_id/inc/topic_nav.inc.php";?>
    <div class="wrap">
        <div class="content">
            <?php if (is_array($article_lists)): ?>
                <ul class="fly-list">
                    <?php foreach ($article_lists as $_article): ?>
                        <li class="fly-list-li">
                            <a class="fly-list-avatar pjax" href="/u/home/<?=$_article['user_id']?>">
                                <img src="<?=create_avatar_url($_article['user_id'], $_article['avatar_ext'])?>" alt="">
                            </a>
                            <h2 class="fly-tip">
                                <a class="pjax" href="/<?=$config['enum_show']['article_type'][$_article['article_type']]?>/detail/<?=$_article['id']?>">
                                    <span class="fly-tip-attile_type_<?=$_article['article_type']?>"><?=$config['enum_show']['article_type_text'][$_article['article_type']]?></span>
                                    <?=xss_filter($_article['article_title'])?>
                                </a>
                                <?php if ($_article['is_top'] == 2): ?>
                                    <span class="fly-tip-stick">置顶</span>
                                <?php endif;?>
                                <?php if ($_article['is_fine'] == 2): ?>
                                    <span class="fly-tip-jing">精帖</span>
                                <?php endif;?>
                            </h2>
                            <p>
                                <span><a class="pjax" href="/u/home/<?=$_article['user_id']?>"><?=$_article['nickname']?><?=create_verify_icon($_article)?></a></span>
                                <span><?=time_tran($_article['add_time'])?></span>
                                <span class="fly-list-hint">
                                    <i class="iconfont" title="评论">&#xe64d;</i> <?=$_article['comment_counts']?>
                                    <i class="iconfont" title="人气">&#xe607;</i> <?=$_article['view_counts']?>
                                </span>
                            </p>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php else: ?>
                <div class="fly-none">并无相关数据</div>
            <?php endif;?>

            <?=$page_html?>
        </div>
    </div>
    <div class="edge">
        <div class="clearfix mb5">
            <span class="fr"><script src='http://git.oschina.net/lostincoding/easyQA/star_widget_preview'></script></span>
            <a class="fr t_red mr5" href="http://git.oschina.net/lostincoding/easyQA/stargazers" target="_blank">本站源码</a>
        </div>
        <?php require_once VIEWPATH . "$theme_id/inc/maopao_mini.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/comment_top_user_lists.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/article_by_view_hot_lists.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/article_by_comment_hot_lists.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/codercalendar.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/friends_link.inc.php";?>
    </div>

<script type="text/javascript">
//相册
layer.photos({
    photos: '.photo'
    ,zIndex: 9999999999,
    shift: 5,
    shade: [0.3, '#000000']
});
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>