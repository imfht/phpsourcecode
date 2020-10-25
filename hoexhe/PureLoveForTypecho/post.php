<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<article id="article" itemscope itemtype="http://schema.org/BlogPosting">
    <header class="entry-header">
        <h1 class="entry-title" itemprop="name headline" itemtype="http://schema.org/Article">
            <?php $this->title() ?>
        </h1>
        <div class="entry-meta">
            <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time> /
            <span itemprop="category"><?php $this->category(','); ?></span> /
            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                <a itemprop="name" class="fn" href="<?php $this->author->permalink(); ?>" rel="author">
                    <?php $this->author(); ?>
                </a>
            </span>
            <span class="breadcrumb" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
                <span>
                    <span>位置：</span>
                    <a href="<?php $this->options->siteUrl(); ?>">Home</a> »
                    <?php $this->category(); ?> »
                    <a itemprop="url" href="<?php $this->permalink() ?>" rel="bookmark">本页</a>
                </span>
            </span>
        </div>
    </header>
    <div id="article-body" class="entry-content font-size-14" itemtype="http://schema.org/Article" itemprop="articleBody">
        <?php $this->content(); ?>
    </div>
    <div class="AdPositionId">
    <!--广告位-->
    </div>
    <div id="heart" class="center">
        <i class="fa fa-heart-o fa-3x" aria-hidden="true"></i>
    </div>

    <span class="poststags clearfix" itemprop="tags">
        <?php $this->tags('', true); ?>
    </span>
    <?php $this->related(6)->to($relatedPosts); ?>
    <?php if ($relatedPosts->have()): ?>
        <div id="related">
            <h3 class="coms_underline">你可能也喜欢</h3>
            <ul>
                <?php while ($relatedPosts->next()): ?>
                    <li>
                        <a href="<?php $relatedPosts->permalink(); ?>" rel="bookmark" title="<?php $relatedPosts->title(); ?>">
                            <?php $relatedPosts->title(26); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>
<?php $this->need('comments.php'); ?>
</article>
<?php $this->need('footer.php'); ?>
