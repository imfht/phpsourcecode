<li class="jieda-daan">
    <div class="detail-about detail-about-reply">
        <a class="jie-user" href="/u/home/<?=$_comment['user_id']?>">
            <img src="<?=create_avatar_url($_comment['user_id'], $_comment['avatar_ext'])?>">
        </a>
    </div>
    <div class="detail-body jieda-body">
        <div class="mb5">
            <div class="votes<?=!empty($_comment['vote_type']) ? ' voted' : ''?>">
                <a class="vote vote_counts" vote_counts="<?=$_comment['vote_counts']?>" href="javascript:;" title="综合得票<?=$_comment['vote_counts']?>"><i class="iconfont"><?=$_comment['vote_counts'] > 0 ? '+' : ''?><?=$_comment['vote_counts']?></i></a>
                <a class="vote<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 1 ? ' active' : ''?>" href="javascript:;" comment_id="<?=$_comment['id']?>" vote_up_counts="<?=$_comment['vote_up_counts']?>"<?=empty($_comment['vote_type']) ? ' onclick="maopao_comment_vote(this, 1);"' : ''?> title="<?=$_comment['vote_up_counts']?>人支持<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 1 ? '，您已支持' : ''?>">
                    <i class="iconfont">&#xe618;</i>
                </a>
                <a class="vote<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 2 ? ' active' : ''?>" href="javascript:;" comment_id="<?=$_comment['id']?>" vote_down_counts="<?=$_comment['vote_down_counts']?>"<?=empty($_comment['vote_type']) ? ' onclick="maopao_comment_vote(this, 2);"' : ''?> title="<?=$_comment['vote_down_counts']?>人反对<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 2 ? '，您已反对' : ''?>">
                    <i class="iconfont">&#xeefe;</i>
                </a>
            </div>
            <a href="/u/home/<?=$_comment['user_id']?>"><?=$_comment['nickname']?><?=create_verify_icon($_comment)?></a>
            <?php if ($maopao['user_id'] == $_comment['user_id']): ?>
                <em>(楼主)</em>
            <?php endif;?>
        </div>
        <?=html_newline(content_xss_filter($_comment['comment_content']))?>
    </div>
    <div class="jieda-reply">
        <span class="time"><?=time_tran($_comment['add_time'])?></span>
        <?php if (!empty($_comment['dialog_id'])): ?>
            <a class="dialog_show_btn" href="javascript:;" dialog_id="<?=$_comment['dialog_id']?>" onclick="maopao_dialog_show(this);"><i class="iconfont">&#xe792;</i>查看对话</a>
        <?php endif;?>
        <a class="reply_show_btn" href="javascript:;" maopao_id="<?=$_comment['maopao_id']?>" comment_id="<?=$_comment['id']?>" dialog_id="<?=$_comment['dialog_id']?>" nickname="<?=$_comment['nickname']?>" onclick="maopao_comment_reply_show(this);"><i class="iconfont">&#xe619;</i>回复</a>
    </div>
</li>