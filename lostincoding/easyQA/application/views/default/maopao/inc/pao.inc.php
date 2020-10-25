<li class="jieda-daan">
    <div class="detail-about detail-about-reply">
        <a class="jie-user pjax" href="/u/home/<?=$_pao['user_id']?>">
            <img src="<?=create_avatar_url($_pao['user_id'], $_pao['avatar_ext'])?>">
        </a>
    </div>
    <div class="detail-body jieda-body">
        <div class="mb5">
            <div class="votes<?=!empty($_pao['vote_type']) ? ' voted' : ''?>">
                <a class="vote vote_counts" vote_counts="<?=$_pao['vote_counts']?>" href="javascript:;" title="综合得票<?=$_pao['vote_counts']?>"><i class="iconfont"><?=$_pao['vote_counts'] > 0 ? '+' : ''?><?=$_pao['vote_counts']?></i></a>
                <a class="vote<?=isset($_pao['vote_type']) && $_pao['vote_type'] == 1 ? ' active' : ''?>" href="javascript:;" maopao_id="<?=$_pao['id']?>" vote_up_counts="<?=$_pao['vote_up_counts']?>"<?=empty($_pao['vote_type']) ? ' onclick="maopao_vote(this, 1);"' : ''?> title="<?=$_pao['vote_up_counts']?>人支持<?=isset($_pao['vote_type']) && $_pao['vote_type'] == 1 ? '，您已支持' : ''?>">
                    <i class="iconfont">&#xe618;</i>
                </a>
                <a class="vote<?=isset($_pao['vote_type']) && $_pao['vote_type'] == 2 ? ' active' : ''?>" href="javascript:;" maopao_id="<?=$_pao['id']?>" vote_down_counts="<?=$_pao['vote_down_counts']?>"<?=empty($_pao['vote_type']) ? ' onclick="maopao_vote(this, 2);"' : ''?> title="<?=$_pao['vote_down_counts']?>人反对<?=isset($_pao['vote_type']) && $_pao['vote_type'] == 2 ? '，您已反对' : ''?>">
                    <i class="iconfont">&#xeefe;</i>
                </a>
            </div>
            <a class="pjax" href="/u/home/<?=$_pao['user_id']?>"><?=$_pao['nickname']?><?=create_verify_icon($_pao)?></a>
        </div>
        <?=html_newline(content_xss_filter($_pao['maopao_content']))?>
    </div>
    <div class="jieda-reply">
        <span class="time"><?=time_tran($_pao['add_time'])?></span>
        <?php if ($_pao['comment_counts'] > 0): ?>
            <a href="/maopao/detail/<?=$_pao['id']?>" target="_blank">评论(<?=$_pao['comment_counts']?>)</a>
        <?php endif;?>
        <a class="reply_show_btn" href="/maopao/detail/<?=$_pao['id']?>" maopao_id="<?=$_pao['id']?>" comment_id="" dialog_id="" nickname="" target="_blank"><i class="iconfont">&#xe619;</i>回复</a>
    </div>
</li>