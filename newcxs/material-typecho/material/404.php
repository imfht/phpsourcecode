<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    <?php $this->need('header.php'); ?>

    <div class="col-md-12">
        <div class="well">
            <div class="text-center">
                <h3>Error 404 - Page Not Found</h3>
                <hr>
                <p><?php _e('你想查看的页面已被转移或删除！'); ?></p>
                <p>&nbsp;</p>
                <p><a href="/" class="btn btn-primary"><?php _e('返回首页'); ?></a></p>
            </div>
        </div>

    </div>
	<?php $this->need('footer.php'); ?>
