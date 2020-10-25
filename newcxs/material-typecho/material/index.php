<?php
/**
 * 这是一款基于 Google Material Design 的 Typecho 主题
 * 
 * @package Material
 * @author newcxs
 * @version 1.0
 * @link http://www.ishw.net
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>

<div class="col-md-9">
    <?php while($this->next()): ?>
        <div class="panel">
            <div class="panel-body">
                <h3 class="post-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h3>
                <div class="post-meta">
                    <span><?php _e('作者: '); ?><a href="<?php $this->author->permalink(); ?>"><?php $this->author(); ?></a> | </span>
                    <span><?php _e('时间: '); ?><time datetime="<?php $this->date('c'); ?>"><?php $this->date('F j, Y'); ?></time> | </span>
                    <span><?php _e('分类: '); ?><?php $this->category(','); ?> | </span>
                    <span><a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('评论', '1 条评论', '%d 条评论'); ?></a></span>
                </div>
                <div class="post-content">
                    <?php $this->content('- 阅读剩余部分 -'); ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <div class="text-center">
        <?php $this->pageNav('&laquo; ', '&raquo;',3,'...',array('wrapClass'=>'pagination','currentClass'=>'active')); ?>
    </div>
</div>

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
