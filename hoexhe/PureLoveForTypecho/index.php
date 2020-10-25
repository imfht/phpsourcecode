<?php
/**
 * PureLoveForTypecho (纯真的爱)
 *
 * <a href="https://www.hoehub.com/PHP/typecho-pureLove.html" target="_blank">💗 主题简介</a>
 * <a href="https://gitee.com/hoexhe/PureLoveForTypecho" target="_blank">🌼 源码下载</a>
 * <a href="https://gitee.com/hoexhe/PureLoveForTypecho/issues target="_blank">🌷 提问/需求</a>
 * @package PureLoveForTypecho
 * @author Hoe
 * @version 1.5.0
 * @link https://www.hoehub.com
 * @time 2018年11月12日11:51
 * @license GPL-3.0
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<?php if ($this->is('index')): // 首页才会显示轮播图 ?>
    <div class="mySliderBar">
        <ul class="rslides" id="slider">
            <?php if ($this->options->banners): ?>
                <?php foreach (json_decode($this->options->banners) as $banner): ?>
                    <li>
                        <img src="<?= $banner->imgUrl; ?>" alt="" title="<?= $banner->desc; ?>">
                        <a href="<?= $banner->url; ?>"><?= $banner->desc; ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif;?>
        </ul>
    </div>
<?php else: // 不是首页 ?>
    <h3 class="archive-title">
        <?php $this->archiveTitle(array(
            'category'  =>  _t('分类【%s】下的文章'),
            'search'    =>  _t('包含关键字【%s】的文章'),
            'tag'       =>  _t('标签【%s】下的文章'),
            'author'    =>  _t('【%s】发布的文章')
        ), '', ''); ?>
    </h3>
<?php endif; // end $this->is('index') ?>
<?php while ($this->next()): ?>
    <article class="posts" itemscope itemtype="http://schema.org/BlogPosting">
        <div class="label">
            <a href="#" rel="category tag"><?php $this->category(','); ?></a>
            <i class="label-arrow"></i>
        </div>
        <h2 class="entry-title">
            <a itemprop="url" href="<?php $this->permalink() ?>" rel="bookmark">
                <span itemprop="name"><?php $this->title() ?></span>
            </a>
        </h2>
        <div class="entry-meta">
            <time datetime="<?php $this->date(); ?>" itemprop="datePublished">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <?php $this->date(); ?>
            </time>
            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                <a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author">
                    <i class="fa fa-user-o" aria-hidden="true"></i>
                    <?php $this->author(); ?>
                </a>
            </span>
            <span itemprop="interactionCount">
                <a itemprop="discussionUrl" href="<?php $this->permalink() ?>#comments">
                    <i class="fa fa-commenting-o" aria-hidden="true"></i>
                    <?php $this->commentsNum('%d'); ?>
                </a>
            </span>
        </div>
        <div class="clearfix"></div>
        <div class="postspicbox">
            <div class="thumbnail">
                <a href="<?php $this->permalink() ?>" title="<?php $this->title(); ?>">
                    <img src="<?= articleThumb($this);?>" alt="<?php $this->title(); ?>">
                </a>
            </div>
        </div>
        <div class="postscontent entry-content" itemprop="articleBody">
            <?php $this->excerpt(180); ?>
        </div>
    </article>
<?php endwhile; ?>
<?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;', 2, '...', ['wrapTag' => 'div', 'wrapClass' => 'pagenavi']); ?>
<?php $this->need('footer.php'); ?>
