<?php
error_reporting(0);
?>
<?php get_header();?>
<body>
<div  class="layoutA layoutA_C clear">
  <div  class="top_mod">
    <div  class="logo_mod"  title="<?php bloginfo('name');?>"  alt="<?php bloginfo('name');?>"><a  href="<?php echo get_option('home'); ?>"><?php bloginfo('name');?></a></div>
</div>
  <div  class="left">
    <div  class="authorBox_mod">
      <h2>博客边栏</h2>
      <div  class="boxA">
        <div  class="list clear">
          <?php get_sidebar();?>
        </div>
      </div>
    </div>
  </div>
  <div  class="right">
    <?php get_template_part("s");?>
    <div  class="articleList_mod">
      <?php get_template_part("meun");?>
      
      <div  class="teamList tj">
        <div  id="lightBlogList">
          <?php  if(have_posts()): while(have_posts()):the_post(); ?>
			
          <div  class="item">
            <div  class="pt">
              <div  class="txt clear">
                <h2  class="clear" ><?php if(is_sticky()){ ?><span  class="tj">推荐</span><?php	}?><?php if(is_new(get_post_time())){ ?><span  class="tj">最新</span><?php	}?><a  href="<?php the_permalink() ?>" title="<?php the_title(); ?>"  ><?php the_title(); ?></a></h2>
                
                <?php if ( has_post_thumbnail() ) { ?><div  class="pic"><?php  the_post_thumbnail('thumbnail'); ?></div><?php }?>
                <div  class="con <?php if ( has_post_thumbnail() ) { ?>haveImg<?php }?>">
                  <?php the_excerpt(); ?>
                  <div  class="data"><span  class="author"><?php the_author_posts_link(); ?></span><span  class="author"><?php the_category(',') ?></span><span  class="time"><?php the_time("H:i:s"); ?></span><span  class="read">阅读(<?php echo getPostViews(get_the_ID());?>次)</span></span><span  class="include"> <a  href="<?php the_permalink() ?>#comment"  target="_blank""><?php comments_number('0', '1', '%' );?></a></span></div>
                </div>
              </div>
            </div>
          </div>

		 



          <?php endwhile;?>
		  <?php else : ?>
		  <?php endif; ?>
           <?php pagenavi();?>
          
        </div>
      </div>
    </div>
     <?php get_template_part("footerLink");?>
    
  </div>
</div>
<?php get_footer();?>