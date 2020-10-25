<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<div class="OMG404">
    <h2>404</h2>
    <a href="<?php $this->options->siteUrl();; ?>">你走错了, 返回首页</a>
</div>
<?php $this->need('footer.php'); ?>