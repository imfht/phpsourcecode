<?php get_header(); ?>
<?php if(have_posts()):while(have_posts()):the_post(); ?>
<main id="post-page" class="container" role="main">
    <div class="row">
        <div class="col-md-10 col-md-push-1">
        
            <article class="panel post tag-ghost-zhu-ti tag-ghost-post tag-roon-io">
                    <header class="panel-heading post-header">
                        <h1 class="post-title">
                            <a><span>Sorry! 404 Not Found</span></a>
                        </h1>
                    </header>

            </article>
        </div>
    </div>
</main>
<?php endwhile;endif; ?>
<?php get_footer(); ?>