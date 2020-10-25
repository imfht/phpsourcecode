<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<article id="article" itemscope itemtype="http://schema.org/BlogPosting">
    <header class="entry-header">
        <h1 class="entry-title" itemprop="name headline" itemtype="http://schema.org/Article">
            <?php $this->title() ?>
        </h1>
        <div class="entry-meta">
            <time datetime="<?php $this->date(); ?>" itemprop="datePublished"><?php $this->date(); ?></time> /
            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                <a itemprop="name" class="fn" href="<?php $this->author->permalink(); ?>" rel="author">
                    <?php $this->author(); ?>
                </a>
            </span>
            <span class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
                <span>
                    <span>位置：</span>
                    <a href="<?php $this->options->siteUrl(); ?>">Home</a> »
                    <a itemprop="url" href="<?php $this->permalink() ?>" rel="bookmark"><?php $this->archiveTitle(null, null, null); ?></a>
                </span>
            </span>
        </div>
    </header>
    <div id="article-body" class="entry-content font-size-14" itemtype="http://schema.org/Article" itemprop="articleBody">
        <?php $this->content(); ?>
    </div>
    <?php $this->need('comments.php'); ?>
</article>
<?php $this->need('footer.php'); ?>
