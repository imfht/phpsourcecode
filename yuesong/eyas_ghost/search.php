<?php get_header(); ?>
    <main class="container" role="main">
        <div class="row">
            <div class="col-sm-10 col-sm-push-1">
            <?php if(have_posts()):while(have_posts()):the_post(); ?>
                <article class="panel post post-<?php echo get_the_id(); ?>" data-scroll-reveal="enter bottom">
                    <header class="panel-heading post-header">
                        <h2 class="post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    </header>
                    <section class="panel-footer post-meta">
                        <div class="meta post-tags">
                            <i class="icon-bookmarks"></i>
                            <?php ey_the_terms_withlink(); ?>
                        </div>
                        <div class="meta post-date">
                            <i class="icon-clock"></i>
                            <?php echo get_the_date(); ?>
                        </div>
                    </section>
                </article>
            <?php endwhile;else:?>
            <article class="panel post post-<?php echo get_the_id(); ?>" data-scroll-reveal="enter bottom">
                <header class="panel-heading post-header">
                    <h2 class="post-title">
                        <a>没有搜索结果</a>
                    </h2>
                </header>
            </article>
	        <?php endif; ?>
            </div>
        </div>

        <nav class="page-pagination">
        <?php ey_pagenav(); ?>
        </nav>
    </main>

<?php get_footer(); ?>