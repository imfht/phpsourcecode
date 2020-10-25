<?php error_reporting(0);?>
<div class="sidebar">
  
  <div class="widget">
            <div class="widget-title">
              <h3>博客信息</h3>
            </div>
            <div class="tagcloud"> <a href='javascript:void(0)' class='tag-link-17' style='font-size: 14px;'>评论总数:<?php echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments");?>条</a> <a href="'javascript:void(0)">博客运营:<?php 
			$starttime = get_option('mytheme_starttime');echo floor((time()-strtotime($starttime))/86400); ?>天</a> <a href='javascript:void(0)' class='tag-link-16'  style='font-size: 14px;'>文章总数:<?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish;?>篇</a>
            <div style="max-width:424px;overflow:hidden;margin-top:5px;margin-bottom:0px;">
			<?php  echo get_option('mytheme_info_bottom');?>
			</div>
</div>
          </div>
  <?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
  <?php endif; ?>
  <div class="widget">
    <div class="widget-title">
      <h3>随机文章</h3>
    </div>
    <ul>
      <?php $rand_posts = get_posts('numberposts=10&orderby=rand');foreach($rand_posts as $post) : ?>
      <li><a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
        </a></li>
      <?php endforeach;?>
    </ul>
  </div>
  
  
  
  <?php  if(is_home()){ ?>
  
    
  <?php
		echo "<div class='widget tagcloud'><div class='widget-title'><h3>友情链接</h3></div>";
		get_links(-1, "<a>", "</a>","", false, "id",false,false, -1,false, true); 
		echo "</div>";
	}else{?>
		
		 
	<?php } ?>



</div>