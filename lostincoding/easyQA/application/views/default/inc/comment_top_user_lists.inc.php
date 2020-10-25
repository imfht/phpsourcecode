<h3 class="page-title">月度雷锋 - TOP 12</h3>
<div class="user-looklog leifeng-rank">
    <span>
        <?php if (is_array($comment_top_user_lists)): ?>
            <?php foreach ($comment_top_user_lists as $_top_user): ?>
                <a class="pjax" href="/u/home/<?=$_top_user['id']?>" title="<?=$_top_user['nickname']?> - <?=$_top_user['comment_counts']?>次评论">
                    <img src="<?=create_avatar_url($_top_user['id'], $_top_user['avatar_ext'])?>">
                    <cite><?=$_top_user['nickname']?></cite>
                    <i><?=$_top_user['comment_counts']?>次评论</i>
                </a>
            <?php endforeach;?>
        <?php endif;?>
    </span>
</div>