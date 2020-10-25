<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <div class="wrap">
        <div class="content w">
            <h2 class="page-title">发布文章内容带有 #话题# 即可生成话题（标题中含有话题无效）。</h2>
            <div class="topic-list">
                <div class="clearfix">
                    <?php if (is_array($topic_lists)): ?>
                        <?php foreach ($topic_lists as $_topic): ?>
                            <div class="item">
                                <a class="img border-radius-5 pjax" href="/topic/articles/<?=$_topic['id']?>">
                                    <img src="/static/default/img/topic.jpg" title="<?=$_topic['topic']?>" alt="<?=$_topic['topic']?>">
                                </a>
                                <p class="clearfix">
                                    <span class="topic-tag">
                                        <a class="text pjax" href="/topic/articles/<?=$_topic['id']?>"><?=$_topic['topic']?></a>
                                    </span>
                                </p>
                                <p class="text-color-999">
                                    <span><?=$_topic['used_times']?> 个讨论</span>
                                </p>
                            </div>
                        <?php endforeach;?>
                    <?php else: ?>
                        <p>无</p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>