<?php get_header();?>
<body>
<div  class="layoutA layoutA_C clear">
  <div  class="top_mod">
    <div  class="logo_mod"  title="<?php bloginfo('name');?>"  alt="<?php bloginfo('name');?>"><a  boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;event_logo&#39;, sBak1:&#39;home&#39;, sBak2:&#39;chenyeyuze&#39;}"  href="<?php echo get_option('home'); ?>"><?php bloginfo('name');?></a></div>
    </div>
      <div  class="left">
    <div  class="authorBox_mod">
      <h2>简洁的边栏</h2>
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
                <h2  class="clear"><a  href="http://dajia.qq.com/blog/408444123423790"   boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;right_list_title&#39;, sBak1:&#39;home&#39;, sBak2:&#39;chenyeyuze&#39;}"><span  class="tj">文章</span><?php the_title(); ?></a></h2>
                <h3></h3>
                <div  class="pic"><img  width="192"  src="<?php bloginfo('template_directory'); ?>/kk.jpg"  style="display: none;"></div>
                <div  class="con">
                  <?php the_excerpt(); ?>
                  <div  class="data"><span  class="author"><?php the_author_posts_link(); ?></span><span  class="author"><?php the_category(',') ?></span></div>
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