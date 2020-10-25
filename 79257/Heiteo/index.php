<?php
/**
 * Heiteo 主题 移植自 Ghost 主题 ghostium ，在此基础上不断修改。
 * 
 * @package Typecho Heiteo Theme 
 * @author nyf.pw
 * @version 1.7
 * @link http://nyf.pw/
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>

<body class="home-template">
<?php $this->need('sidebar.php'); ?>
<div class="drawer-overlay"></div>
<main id="container" role="main" class="container">
<div class="surface">
<div class="surface-container">
<div data-pjax-container class="content">
<aside role="banner" class="cover">
  <div data-load-image="<?php $this->options->imgUrl(); ?>" class="cover-image"></div>
  <div class="cover-container"> <a href="<?php $this->options->siteUrl(); ?>" class="cover-logo" data-pjax> <img src="<?php $this->options->logoUrl(); ?>"> </a>
    <h1 class="cover-title"><?php $this->options->title(); ?></h1>
    <p class="cover-description"><?php $this->options->description(); ?></p>
  </div>
</aside>
<section class="wrapper" tabindex="0">
<div class="wrapper-container">
<section class="post-list">
<?php while($this->next()): ?>
  <article itemscope role="article" class="post-item post tag-getting-started">
    <header class="post-item-header">
      <h2 itemprop="name" class="post-item-title"> <a href="<?php $this->permalink(); ?>" itemprop="url" data-pjax title="Welcome to Ghost"><?php $this->title(); ?></a> </h2>
    </header>
    <section itemprop="description" class="post-item-excerpt">
      <p>
	  <?php if (!empty($this->options->more) && in_array('MoreStatus', $this->options->more)): ?>
      <?php if ($this->options->moretext == ''):?>
	  <?php $this->content('查看全文...'); ?>
      <?php else :?>
      <?php $moretext = $this->options->moretext;$this->content($moretext); ?>
      <?php endif; ?>
      <?php endif; ?>
      </p>
    </section>
    <footer class="post-item-footer">
      <ul class="post-item-meta-list">
        <li class="post-item-meta-item">
          <time datetime="2013-11-11" itemprop="datePublished"><?php $this->date("Y-m-d H:i:s"); ?></time>
        </li>
        <li class="post-item-meta-item"> <?php $this->tags(' & ', true, 'none'); ?> </li>
        <li class="post-item-meta-item"> <?php $this->commentsNum('%d 个评论'); ?></li>
      </ul>
    </footer>
  </article>
<?php endwhile; ?>

</section>
<nav role="pagination" class="post-list-pagination"> <span class="post-list-pagination-item post-list-pagination-item-current"><?php $this->pageNav(); ?></span> </nav>
<?php $this->need('footer.php'); ?>
