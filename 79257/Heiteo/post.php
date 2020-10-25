<?php $this->need('header.php'); ?>

<body class="<?php if ($this->options->siderbar == 0) {echo 'post-template tag-getting-started  pace-done drawer-open drawer-transition';} else {echo 'post-template tag-getting-started pace-done';} ?>">
<?php $this->need('sidebar.php'); ?>
<div class="drawer-overlay"></div>
<main id="container" role="main" class="container">
<div class="surface">
<div class="surface-container">
<div data-pjax-container class="content">
<section class="wrapper wrapper-post">
<div class="wrapper-container">
<article itemscope role="article" class="post post tag-getting-started">
  <section class="post-container">
    <header class="post-header">
      <ul class="post-meta-list">
        <li class="post-meta-item">
          <time datetime="<?php $this->date("Y-m-d H:i:s"); ?>" itemprop="datePublished">
            <?php $this->date("Y-m-d H:i:s"); ?>
          </time>
        </li>
        <li class="post-meta-item">
          <?php $this->tags(' & ', true, 'none'); ?>
        </li>
        <li class="post-meta-item">
          <?php $this->category(' & '); ?>
        </li>
        <li class="post-meta-item">
          <?php $this->commentsNum('%d 个评论'); ?>
        </li>
      </ul>
      <h1 itemprop="name headline" class="post-title"><a href="<?php $this->permalink(); ?>" itemprop="url" data-pjax title="<?php $this->title(); ?>">
        <?php $this->title(); ?>
        </a></h1>
      
      <!--h2 itemprop="about" class="post-subtitle"></h2--> 
    </header>
    <aside class="post-side">
      <div class="post-author"> <a href="<?php $this->author->permalink(); ?>" class="post-author-avatar"> <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->author(); ?>"> </a>
        <div class="post-author-info"> <a href="<?php $this->author->permalink(); ?>" class="post-author-name">
          <?php $this->author(); ?>
          </a>
          <p class="post-author-bio">
            <?php $this->options->description(); ?>
          </p>
        </div>
      </div>
    </aside>
    <div itemprop="articleBody" class="post-body">
      <?php $this->content(); ?>
    </div>
  </section>
  <section itemprop="comment" class="post-comments">
    <div id="disqus_thread">
	
	<?php if (!empty($this->options->duoshuo) && in_array('PostShowDuoshuo', $this->options->duoshuo)): ?>
	<?php include('comments.php'); ?>
	<?php endif; ?>
	
	</div>
  </section>
</article>
<?php $this->need('footer.php'); ?>
