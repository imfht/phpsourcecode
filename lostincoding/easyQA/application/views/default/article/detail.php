<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content detail pr">
            <a class="pjax" href="/u/home/<?=$article['user_id']?>" title="<?=$article['nickname']?>"><img class="avatar" src="<?=create_avatar_url($article['user_id'], $article['avatar_ext'])?>"></a>
            <h1><?=xss_filter($article['article_title'])?></h1>
            <div class="fly-tip fly-detail-hint">
                <div class="mr10 dib">
                    <span class="fly-tip-attile_type_<?=$article['article_type']?>"><?=$config['enum_show']['article_type_text'][$article['article_type']]?></span>
                    <?php if ($article['is_top'] == 2): ?>
                        <span class="fly-tip-stick">置顶</span>
                    <?php endif;?>
                    <?php if ($article['is_fine'] == 2): ?>
                        <span class="fly-tip-jing">精帖</span>
                    <?php endif;?>
                    <?php if ($article['article_type'] == 1): ?>
                        <?php if ($article['article_status'] == 2): ?>
                            <span class="fly-tip-jie">已采纳</span>
                        <?php else: ?>
                            <span>未采纳</span>
                        <?php endif;?>
                    <?php endif;?>
                </div>
                <div class="votes<?=!empty($article['vote_type']) ? ' voted' : ''?>">
                    <a class="vote vote_counts" vote_counts="<?=$article['vote_counts']?>" href="javascript:;" title="综合得票<?=$article['vote_counts']?>"><i class="iconfont"><?=$article['vote_counts'] > 0 ? '+' : ''?><?=$article['vote_counts']?></i></a>
                    <a class="vote<?=isset($article['vote_type']) && $article['vote_type'] == 1 ? ' active' : ''?>" href="javascript:;" article_id="<?=$article['id']?>" vote_up_counts="<?=$article['vote_up_counts']?>"<?=empty($article['vote_type']) ? ' onclick="article_vote(this, 1);"' : ''?> title="<?=$article['vote_up_counts']?>人支持<?=isset($article['vote_type']) && $article['vote_type'] == 1 ? '，您已支持' : ''?>">
                        <i class="iconfont">&#xe618;</i>
                    </a>
                    <a class="vote<?=isset($article['vote_type']) && $article['vote_type'] == 2 ? ' active' : ''?>" href="javascript:;" article_id="<?=$article['id']?>" vote_down_counts="<?=$article['vote_down_counts']?>"<?=empty($article['vote_type']) ? ' onclick="article_vote(this, 2);"' : ''?> title="<?=$article['vote_down_counts']?>人反对<?=isset($article['vote_type']) && $article['vote_type'] == 2 ? '，您已反对' : ''?>">
                        <i class="iconfont">&#xeefe;</i>
                    </a>
                </div>
                <a class="pjax" href="/u/home/<?=$article['user_id']?>">
                    <?=$article['nickname']?><?=create_verify_icon($article)?>
                    <?=time_tran($article['add_time'])?>发布
                </a>
                <?php if (isset($user) && $user['id'] == $article['user_id']): ?>
                    <a class="jie-admin" href="/article/edit/<?=$article['id']?>">编辑此文</a>
                <?php endif;?>
                <div class="fly-list-hint">
                    <i class="iconfont" title="评论">&#xe64d;</i> <?=$article['comment_counts']?>
                    <i class="iconfont" title="人气">&#xe607;</i> <?=$article['view_counts']?>
                </div>
            </div>

            <div class="detail-body" style="margin-bottom: 20px;">
                <?=html_newline(content_xss_filter($article['article_content']))?>
                <?php if (is_array($article_append_lists)): ?>
                    <div class="article_append_lists" style="margin-bottom: 20px;">
                        <?php foreach ($article_append_lists as $_article_append): ?>
                            <div class="article_append">
                                <div class="t_graw mb5">
                                    <span>追加内容</span>
                                    <span><?=time_tran($_article_append['append_time'])?></span>
                                </div>
                                <div><?=html_newline(content_xss_filter($_article_append['append_content']))?></div>
                            </div>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
            </div>

            <div class="clearfix mb10">
                <!--分享按钮-->
                <span class="share_btns_wrap">
                    <a class="share_btn" title="分享"><i class="iconfont">&#xe617;</i></a>
                    <div class="sharebox">
                        <div class="up-arrow"></div>
                        <div class="bdsharebuttonbox" data-tag="share_article">
                            <a class="bds_tsina" data-cmd="tsina"><i class="iconfont">&#xe603;</i> 新浪微博</a>
                            <a class="bds_qzone" data-cmd="qzone"><i class="iconfont">&#xe62f;</i> QQ空间</a>
                            <a class="bds_sqq" data-cmd="sqq"><i class="iconfont">&#xe602;</i> QQ好友/群</a>
                            <a class="bds_weixin" data-cmd="weixin"><i class="iconfont">&#xe62a;</i> 微信</a>
                            <a class="popup_more" data-cmd="more"><i class="iconfont">&#xe62b;</i> 更多</a>
                        </div>
                    </div>
                </span>
                <?php if (empty($article['favorite_id'])): ?>
                    <a class="favorite_btn mr15" href="javascript:;" article_id="<?=$article['id']?>" favorited="0" title="收藏" onclick="article_favorite(this);"><i class="iconfont">&#xe60a;</i></a>
                <?php else: ?>
                    <a class="favorite_btn mr15" href="javascript:;" article_id="<?=$article['id']?>" favorited="1" title="取消收藏" onclick="article_favorite(this);"><i class="iconfont">&#xe609;</i></a>
                <?php endif;?>
            </div>

            <a name="comment"></a>
            <h2 class="page-title">评论<span>（<em id="jiedaCount"><?=$comment_counts?></em>）</span></h2>

            <ul class="jieda">
                <?php if (is_array($comment_lists)): ?>
                    <?php foreach ($comment_lists as $_comment): ?>
                        <li class="jieda-daan">
                            <div class="detail-about detail-about-reply">
                                <a class="jie-user" href="/u/home/<?=$_comment['user_id']?>">
                                    <img src="<?=create_avatar_url($_comment['user_id'], $_comment['avatar_ext'])?>">
                                </a>
                            </div>
                            <div class="detail-body jieda-body">
                                <?php if ($_comment['comment_status'] == 2): ?>
                                    <i class="iconfont icon_accept" title="最佳答案">&#xe630;</i>
                                <?php endif;?>
                                <div class="mb5">
                                    <div class="votes<?=!empty($_comment['vote_type']) ? ' voted' : ''?>">
                                        <a class="vote vote_counts" vote_counts="<?=$_comment['vote_counts']?>" href="javascript:;" title="综合得票<?=$_comment['vote_counts']?>"><i class="iconfont"><?=$_comment['vote_counts'] > 0 ? '+' : ''?><?=$_comment['vote_counts']?></i></a>
                                        <a class="vote<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 1 ? ' active' : ''?>" href="javascript:;" comment_id="<?=$_comment['id']?>" vote_up_counts="<?=$_comment['vote_up_counts']?>"<?=empty($_comment['vote_type']) ? ' onclick="comment_vote(this, 1);"' : ''?> title="<?=$_comment['vote_up_counts']?>人支持<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 1 ? '，您已支持' : ''?>">
                                            <i class="iconfont">&#xe618;</i>
                                        </a>
                                        <a class="vote<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 2 ? ' active' : ''?>" href="javascript:;" comment_id="<?=$_comment['id']?>" vote_down_counts="<?=$_comment['vote_down_counts']?>"<?=empty($_comment['vote_type']) ? ' onclick="comment_vote(this, 2);"' : ''?> title="<?=$_comment['vote_down_counts']?>人反对<?=isset($_comment['vote_type']) && $_comment['vote_type'] == 2 ? '，您已反对' : ''?>">
                                            <i class="iconfont">&#xeefe;</i>
                                        </a>
                                    </div>
                                    <a href="/u/home/<?=$_comment['user_id']?>"><?=$_comment['nickname']?><?=create_verify_icon($_comment)?></a>
                                    <?php if ($article['user_id'] == $_comment['user_id']): ?>
                                        <em>(楼主)</em>
                                    <?php endif;?>
                                </div>
                                <?=html_newline(content_xss_filter($_comment['comment_content']))?>
                            </div>
                            <div class="jieda-reply">
                                <span class="time"><?=time_tran($_comment['add_time'])?></span>
                                <?php if (!empty($_comment['dialog_id'])): ?>
                                    <a class="dialog_show_btn" href="javascript:;" dialog_id="<?=$_comment['dialog_id']?>" onclick="dialog_show(this);"><i class="iconfont">&#xe792;</i>查看对话</a>
                                <?php endif;?>
                                <a class="reply_show_btn" href="javascript:;" comment_id="<?=$_comment['id']?>" dialog_id="<?=$_comment['dialog_id']?>" nickname="<?=$_comment['nickname']?>" onclick="comment_reply_show(this);"><i class="iconfont">&#xe619;</i>回复</a>
                                <?php if (isset($user) && $article['user_id'] == $user['id'] && $article['article_type'] == 1 && $article['article_status'] != 2): ?>
                                    <a class="accept_btn" href="javascript:;" onclick="comment_accept(<?=$_comment['id']?>);">采纳</a>
                                <?php endif;?>
                            </div>
                        </li>
                    <?php endforeach;?>
                <?php else: ?>
                    <li class="fly-none">没有任何评论</li>
                <?php endif;?>
            </ul>

            <?php if (isset($user)): ?>
                <div class="layui-form layui-form-pane">
                    <form method="post" comment_id="" dialog_id="" onsubmit="return comment_add(this);">
                        <div id="comment_rich_editor"></div>
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
        <?php require_once VIEWPATH . "$theme_id/inc/article_by_view_hot_lists.inc.php";?>
        <?php require_once VIEWPATH . "$theme_id/inc/article_by_comment_hot_lists.inc.php";?>
    </div>

<script type="text/javascript">
var article_id = <?=$article['id']?>;
create_element('css', '<?=$config['files']['web']['highlight.css']?>');
create_element('js', '<?=$config['files']['web']['highlight.js']?>');
create_element('js', '/static/' + CONFIG['theme_id'] + '/js/article/detail.min.js');
</script>

</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>