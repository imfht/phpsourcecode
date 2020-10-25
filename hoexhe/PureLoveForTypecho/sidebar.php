<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<aside class="sidebar">
    <?php if ($this->options->sidebarBlock && in_array('showSiteStatistics', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3>网站统计</h3>
            <ul class="blogroll">
                <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
                <li>文章总数：<?php $stat->publishedPostsNum() ?></li>
                <li>分类总数：<?php $stat->categoriesNum() ?></li>
                <li>评论总数：<?php $stat->publishedCommentsNum() ?></li>
                <li>页面总数：<?= $stat->publishedPagesNum + $stat->publishedPostsNum; ?></li>
                <li>标签总数：<?= getTagCount(); ?></li>
                <li>占个位子：<i class="fa fa-heartbeat fa-lg" aria-hidden="true"></i></li>
            </ul>
        </section>
    <?php endif; ?>
    <?php if ($this->options->sidebarBlock && in_array('showRecentComments', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3>最近回复</h3>
            <div class="textwidget">
                <ul class="commentsArea">
                    <?php $this->widget('Widget_Comments_Recent', 'ignoreAuthor=true')->to($comments); ?>
                    <?php while ($comments->next()): ?>
                        <a class="comment-item" href="<?php $comments->permalink(); ?>">
                            <img src="<?= getCommentAvatarUrl($comments); ?>" alt="评论头像" title="<?= str_replace(['<', '>', '"'], '', $comments->text); ?>">
                        </a>
                    <?php endwhile; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($this->options->sidebarBlock && in_array('showHotPosts', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3>热门文章</h3>
            <div class="textwidget">
                <ul>
                    <?php hotPosts($result, 10); ?>
                    <?php foreach ($result as $post): ?>
                        <li>
                            <a href="<?= $post['permalink']; ?>"
                               title="<?= '评论数: ' . $post['commentsNum']; ?>"><?= $post['title']; ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($this->options->sidebarBlock && in_array('showRecentPosts', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3>最新文章</h3>
            <div class="textwidget">
                <ul>
                    <?php $this->widget('Widget_Contents_Post_Recent')
                        ->parse('<li><a href="{permalink}" title="{title}">{title}</a></li>'); ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($this->options->sidebarBlock && in_array('showTagCloud', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3>标签云</h3>
            <div class="textwidget">
                <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=30')->to($tags); ?>
                <?php if ($tags->have()): ?>
                    <div id="tag-cloud">
                        <?php while ($tags->next()): ?>
                            <a href="<?php $tags->permalink(); ?>" rel="tag" class="size-<?php $tags->split(5, 10, 20, 30); ?>" title="<?= $tags->count() . '个话题'; ?> ">
                                <?php $tags->name(); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <li>没有任何标签</li>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($this->options->sidebarBlock && in_array('showArchive', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3 class="widget-title">归档</h3>
            <div class="textwidget">
                <ul>
                    <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=Y-m-d&limit=6')
                        ->parse('<li><a href="{permalink}">{date}</a></li>'); ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($this->options->sidebarBlock && in_array('showOther', $this->options->sidebarBlock)): ?>
        <section class="widgetbox">
            <h3 class="widget-title">其它</h3>
            <div class="textwidget">
                <ul class="">
                    <?php if ($this->user->hasLogin()): ?>
                        <li class="last"><a href="<?php $this->options->adminUrl(); ?>">进入后台 (<?php $this->user->screenName(); ?>)</a></li>
                        <li><a href="<?php $this->options->logoutUrl(); ?>">退出</a></li>
                    <?php else: ?>
                        <li class="last"><a href="<?php $this->options->adminUrl('login.php'); ?>">登录</a></li>
                    <?php endif; ?>
                    <li><a href="<?php $this->options->feedUrl(); ?>">文章 RSS</a></li>
                    <li><a href="<?php $this->options->commentsFeedUrl(); ?>">评论 RSS</a></li>
                    <li><a href="http://www.typecho.org">Typecho</a></li>
                </ul>
            </div>
        </section>
    <?php endif; ?>
</aside>
