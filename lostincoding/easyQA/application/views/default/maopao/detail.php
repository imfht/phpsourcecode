<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content detail pr">
            <?php require_once VIEWPATH . "$theme_id/maopao/inc/nav.inc.php";?>
            <div class="fly-tip fly-detail-hint">
                <div class="votes<?=!empty($maopao['vote_type']) ? ' voted' : ''?>">
                    <a class="vote vote_counts" vote_counts="<?=$maopao['vote_counts']?>" href="javascript:;" title="综合得票<?=$maopao['vote_counts']?>"><i class="iconfont"><?=$maopao['vote_counts'] > 0 ? '+' : ''?><?=$maopao['vote_counts']?></i></a>
                    <a class="vote<?=isset($maopao['vote_type']) && $maopao['vote_type'] == 1 ? ' active' : ''?>" href="javascript:;" maopao_id="<?=$maopao['id']?>" vote_up_counts="<?=$maopao['vote_up_counts']?>"<?=empty($maopao['vote_type']) ? ' onclick="maopao_vote(this, 1);"' : ''?> title="<?=$maopao['vote_up_counts']?>人支持<?=isset($maopao['vote_type']) && $maopao['vote_type'] == 1 ? '，您已支持' : ''?>">
                        <i class="iconfont">&#xe618;</i>
                    </a>
                    <a class="vote<?=isset($maopao['vote_type']) && $maopao['vote_type'] == 2 ? ' active' : ''?>" href="javascript:;" maopao_id="<?=$maopao['id']?>" vote_down_counts="<?=$maopao['vote_down_counts']?>"<?=empty($maopao['vote_type']) ? ' onclick="maopao_vote(this, 2);"' : ''?> title="<?=$maopao['vote_down_counts']?>人反对<?=isset($maopao['vote_type']) && $maopao['vote_type'] == 2 ? '，您已反对' : ''?>">
                        <i class="iconfont">&#xeefe;</i>
                    </a>
                </div>
                <a class="pjax" href="/u/home/<?=$maopao['user_id']?>">
                    <?=$maopao['nickname']?><?=create_verify_icon($maopao)?>
                    <?=time_tran($maopao['add_time'])?>发布
                </a>
            </div>

            <div class="detail-body" style="margin-bottom: 20px;">
                <?=html_newline(content_xss_filter($maopao['maopao_content']))?>
            </div>

            <a name="comment"></a>
            <h2 class="page-title">评论<span>（<em id="jiedaCount"><?=$comment_counts?></em>）</span></h2>

            <ul class="jieda">
                <?php if (is_array($comment_lists)): ?>
                    <?php foreach ($comment_lists as $_comment): ?>
                        <?php require VIEWPATH . "$theme_id/maopao/inc/comment.inc.php";?>
                    <?php endforeach;?>
                <?php else: ?>
                    <li class="fly-none">没有任何评论</li>
                <?php endif;?>
            </ul>

            <?php if (isset($user)): ?>
                <div class="layui-form layui-form-pane">
                    <form method="post" maopao_id="<?=$maopao['id']?>" comment_id="" dialog_id="" onsubmit="return maopao_comment_add(this);">
                        <div id="maopao_comment_rich_editor"></div>
                        <div class="layui-form-item">
                            <button type="submit" class="layui-btn">提交评论</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div>评论请先<a class="t_orange" href="/account/signin">登录</a></div>
            <?php endif;?>
        </div>
    </div>
    <div class="edge">
        <div class="maopao_wrap" class="clearfix">
            <?php require_once VIEWPATH . "$theme_id/inc/maopao_by_comment_hot_lists.inc.php";?>
        </div>
        <?php require_once VIEWPATH . "$theme_id/inc/friends_link.inc.php";?>
    </div>

<script type="text/javascript">
//创建富文本编辑器
$(function(){
    create_rich_editor('maopao_comment_rich_editor', '', '请输入冒泡内容', 40, false);
});

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