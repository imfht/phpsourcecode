<h3 class="page-title">最近热帖</h3>
<ol class="fly-list-one mb15">
    <?php if (is_array($q_by_view_hot_lists)): ?>
        <?php foreach ($q_by_view_hot_lists as $_q): ?>
            <li>
                <a class="pjax" href="/q/detail/<?=$_q['id']?>">
                	<img class="avatar" src="<?=create_avatar_url($_q['user_id'], $_q['avatar_ext'])?>">
                	<?=xss_filter($_q['article_title'])?>
                </a>
                <span><?=$_q['view_counts']?> <i class="iconfont">&#xe607;</i></span>
            </li>
        <?php endforeach;?>
    <?php endif;?>
</ol>