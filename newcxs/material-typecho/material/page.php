<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="col-md-9">
    <div class="panel">
        <div class="panel-body">
            <h3 class="post-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h3>
            <div class="post-content">
                <?php $this->content(); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <?php $this->need('comments.php'); ?>
    </div>
</div>

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
