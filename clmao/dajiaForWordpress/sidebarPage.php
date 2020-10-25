<div  class="left">
    <div  class="author_mod clear ">
      <div  class="face"> <a  href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>"  ><span><?php global $authordata; if (function_exists('get_avatar')) { echo get_avatar( get_the_author_email(), '102',"",get_the_author());  }?></span></a> </div>
      <div  class="name"> <a  href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>"  ><?php echo get_the_author();?></a> </div>
      <div  class="text tj"> <?php $des=get_usermeta($authordata->ID);echo '个性签名：'.$des[3];?><br/><a  href="<?php the_permalink() ?>#comments"  class="c1">评论本文</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>"  class="c1">站内专栏</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href="<?php echo $authordata->user_url;?>"  class="c2"  target="_blank">Ta的博客</a>
         <div style="margin-top:10px;"><?php  echo get_option('mytheme_content_right');?></div>
	  </div>
    </div>
    
  </div>