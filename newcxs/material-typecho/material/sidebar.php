<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div class="col-md-3">

    <form class="panel-body" id="search" method="post" action="./" role="search">
        <div class="input-group">
            <input type="text" placeholder="搜索" class="form-control" name="s" >
            <span class="input-group-btn">
                <button class="btn btn-primary btn-fab btn-raised mdi-action-search" id="search-btn" type="submit"></button>
            </span>
        </div>
    </form>

    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentPosts', $this->options->sidebarBlock)): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('最新文章'); ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('Widget_Contents_Post_Recent')
            ->parse('<a class="item" href="{permalink}">{title}</a>'); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowRecentComments', $this->options->sidebarBlock)): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('最近回复'); ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
            <?php while($comments->next()): ?>
                <a class="item" href="<?php $comments->permalink(); ?>"><?php $comments->author(false); ?>: <?php $comments->excerpt(35, '...'); ?></a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowCategory', $this->options->sidebarBlock)): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('分类'); ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('Widget_Metas_Category_List')->listCategories('wrapClass=category-list&itemClass=item'); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowArchive', $this->options->sidebarBlock)): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('归档'); ?></h3>
        </div>
        <div class="panel-body">
            <?php $this->widget('Widget_Contents_Post_Date', 'type=month&format=F Y')
            ->parse('<a class="item" href="{permalink}">{date}</a>'); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->options->sidebarBlock) && in_array('ShowOther', $this->options->sidebarBlock)): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('其它'); ?></h3>
        </div>
        <div class="panel-body">
            <?php if($this->user->hasLogin()): ?>
                <a class="item" href="<?php $this->options->adminUrl(); ?>"><?php _e('进入后台'); ?> (<?php $this->user->screenName(); ?>)</a>
                <a class="item" href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a>
            <?php else: ?>
                <a class="item" href="<?php $this->options->adminUrl('login.php'); ?>"><?php _e('登录'); ?></a>
            <?php endif; ?>
            <a class="item" href="<?php $this->options->feedUrl(); ?>"><?php _e('文章 RSS'); ?></a>
            <a class="item" href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论 RSS'); ?></a>
            <a class="item" href="http://www.typecho.org">Typecho</a>
            <a class="item" href="http://www.ishw.net">Andy Blog</a>
        </div>
    </div>
<?php endif; ?>
</div>
