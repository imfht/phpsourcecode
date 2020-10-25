<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
/**
 * 自定义归档页
 * @package custom
 * @link http://docs.typecho.org/themes/custom-theme#%E8%87%AA%E5%AE%9A%E4%B9%89%E9%A1%B5%E9%9D%A2_page_%E6%A8%A1%E6%9D%BF
 */
$colors = ['#ff8888', '#1c87bf', '#95c91e', '#ffb902', '#d32d93'];
?>
<article id="article" itemscope="" itemtype="http://schema.org/BlogPosting">
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
    <div id="article-body" class="entry-content">
        <?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives);?>
        <div class="timeline">
            <article>
                <?php while ($archives->next()): ?>
                    <section>
                        <span class="point-time" style="background: <?= $colors[rand(0, 4)]; ?>; "></span>
                        <time datetime="<?= date('Y', $archives->created); ?>">
                            <span><?= date('M-d l', $archives->created); ?></span>
                            <span>
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <?= date('Y', $archives->created); ?>
                    </span>
                        </time>
                        <aside>
                            <p itemprop="name headline" itemtype="http://schema.org/Article">
                                <a class="title" href="<?php $archives->permalink(); ?>" title="<?= date('Y-m-d H:i', $archives->created); ?>">
                                    <?php $archives->title(); ?>
                                </a>
                            </p>
                            <p class="brief" itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <a class="" itemprop="name" href="<?php $archives->author->permalink(); ?>" rel="author">
                                    <i class="fa fa-user-o" aria-hidden="true"></i>
                                    <?php $archives->author(); ?>
                                </a>
                                <span>
                            <i class="fa fa-tags" aria-hidden="true"></i>
                                    <?php $archives->tags();?>
                        </span>
                            </p>
                        </aside>
                    </section>
                <?php endwhile;?>
            </article>
        </div>
    </div>
</article>
<?php $this->need('footer.php');?>

