<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

    <div class="col-md-9" role="main">
        <div class="alert alert-success"><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ''); ?></div>
        <?php if ($this->have()): ?>
        <?php while($this->next()): ?>
        <div class="panel">
            <div class="panel-body">
                <h3 class="post-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h3>
                <div class="post-meta">
                    <span><?php _e('作者: '); ?><a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author"><?php $this->author(); ?></a> | </span>
                    <span><?php _e('时间: '); ?><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date('F j, Y'); ?></time> | </span>
                    <span><?php _e('分类: '); ?><?php $this->category(','); ?> | </span>
                    <span><a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('评论', '1 条评论', '%d 条评论'); ?></a></span>
                </div>
                <div class="post-content">
                    <?php $this->content('- 阅读剩余部分 -'); ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning"><?php _e('没有找到内容'); ?></div>
        <?php endif; ?>

        <div class="text-center">
            <?php $this->pageNav('&laquo; ', '&raquo;',3,'...',array('wrapClass'=>'pagination','currentClass'=>'active')); ?>
        </div>
    </div>

	<?php $this->need('sidebar.php'); ?>
	<?php $this->need('footer.php'); ?>
