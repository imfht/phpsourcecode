<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>

<div id="main" class="main layui-clear" style="margin-top: 300px;">
    <div class="fly-home">
        <img class="avatar" src="<?=create_avatar_url($huser['id'], $huser['avatar_ext'])?>" alt="<?=$huser['nickname']?>">
        <h1><?=$huser['nickname']?>
            <?php if ($huser['gender'] == 'm'): ?>
                <i class="iconfont icon_gender_<?=$huser['gender']?>">&#xe606;</i>
            <?php elseif ($huser['gender'] == 'f'): ?>
                <i class="iconfont icon_gender_<?=$huser['gender']?>">&#xe66b;</i>
            <?php endif;?>
        </h1>
        <div>积分：<?=isset($huser['points']) ? $huser['points'] : 0?></div>
        <?=create_verify_info($huser)?>
        <div class="layui-main">
            <p style="display: inline-block; max-width: 800px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #999; margin-top: 10px;">
                <?=!empty($huser['brief']) ? $huser['brief'] : '这个人懒得留下签名'?>
            </p>
        </div>
    </div>
    <div class="fly-main layui-clear">
        <div class="home-nav">
            <a href="/u/home/<?=$huser['id']?>">主页</a>
        </div>
        <div class="home-left">
            <h2>最近发表的求解</h2>
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
                            <a href="/q/detail/<?=$_article['id']?>" target="_blank"><?=xss_filter($_article['article_title'])?></a>
                            <i><?=time_tran($_article['add_time'])?></i>
                            <em><?=$_article['comment_counts']?>评/<?=$_article['view_counts']?>阅</em>
                        </li>
                    <?php endforeach;?>
                <?php else: ?>
                    <li class="fly-none" style="min-height: 50px; padding:30px 0; height:auto;"><i style="font-size:14px;">没有发表任何求解</i></li>
                <?php endif;?>
            </ul>
            <h2 style="margin-top:30px;">最近的评论</h2>
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
        <div class="home-right">
            <ul class="home-info">
                <?php if (!(isset($user) && is_array($user) && $user['id'] == $huser['id'])): ?>
                    <li class="tc mb20"><?=relationship_html($huser['id'])?></li>
                <?php endif;?>
                <li class="tc"><span><i class="iconfont">&#xe622;</i>加入时间：<?=date('Y-m-d', strtotime($huser['signup_time']))?></span></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>