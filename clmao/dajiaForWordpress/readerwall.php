<?php
/*
  Template Name: readerwall
 */
 get_header();
function the_readerwall($limit_number=49){
	//if( $mostactive = get_option( 'mostactive' ) ){
		//echo $mostactive;
	//}else{
		$comments = get_comments( array('status' => 'approve') );//获取评论
		$my_email = get_option( 'admin_email' );
		$mostactive = array();
		$tmp = array();
		$comment_date = array();
		
		foreach( $comments as $comment ){//计算每个人评论数
			$author = $comment -> comment_author;
			$email = $comment -> comment_author_email;
			$url = $comment -> comment_author_url;
			$date = $comment -> comment_date;
			$comment_year = explode("-" ,$date);
			if ( $comment_year[0] < date("Y") ){//今年的评论
				break;
			}
			if( ($email != $my_email) ){
				$i = -1;
				$index = -1;
				foreach( $mostactive as $item => $comm ){
					if( $email == $comm["email"] ){
						$index = $item;
						break;
					}
				}

				if( $index > -1 ){
					$mostactive[$index]["number"] += 1;
				}else{
					array_push($mostactive,array( "author" => $author, "email" => $email, "url" => $url, "date" => $date, "number" => 1 ));
				}
			}
		}
		
		//数组按评论数逆序排序
		foreach( $mostactive as $item => $comm){
			$tmp[$item] = $comm['number'];
			$comment_date[$item] = $comm['date'];
		}
		array_multisort( $tmp, SORT_DESC, $comment_date, SORT_ASC, $mostactive );//评论数相同时按照最后评论时间升序排序
		
		if( empty($mostactive) ){
			$output = '<ul><li>none data.</li></ul>';
		}else{
			$output = '<ul>';
			foreach( $mostactive as $item => $comm){
				$avatar = get_avatar($comm["email"], 90,'',$author);
				$author = $comm["author"];
				$url = $comm["url"];
				$number = $comm["number"];
				if($author!="撒哈拉De小猫" && $author!="撒哈拉的小猫"){$output.='<li>' . '<a rel="external nofollow" href="'. $url . '" target="_blank" title="今年'. $number .'条评论">' . $avatar .'<span class="wall_name">'.$author.'</span></a></li>';
				$limit_number--;
				}
				if ( $limit_number <= 0 ){
					break;
				}
			}
			$output .= '</ul>';
		}
		echo '<div id="readerwall">'.$output.'</div>';
		//update_option('mostactive', $output);
	} 
 
 ?>
<body>

<div  class="layoutB clear">
  <div  class="top_mod">
   <div  class="logo_mod"  title="<?php bloginfo('name');?>"  alt="<?php bloginfo('name');?>"><a  boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;event_logo&#39;, sBak1:&#39;home&#39;, sBak2:&#39;chenyeyuze&#39;}"  href="<?php echo get_option('home'); ?>"><?php bloginfo('name');?></a></div>
    <div  class="topBar_mod"><?php  $str=wp_nav_menu(
				array(
				'theme_location'  => '', //指定显示的导航名，如果没有设置，则显示第一个
				'menu'            => '',
				'container'       => '', //最外层容器标签名
				'container_class' => 'primary', //最外层容器class名
				'container_id'    => '',//最外层容器id值
				'menu_class'      => 'sf-menu', //ul标签class
				'menu_id'         => '',//ul标签id
				'echo'            => false,//是否打印，默认是true，如果想将导航的代码作为赋值使用，可设置为false
				'fallback_cb'     => 'wp_page_menu',//备用的导航菜单函数，用于没有在后台设置导航时调用
				'before'          => '',//显示在导航a标签之前
				'after'           => '',//显示在导航a标签之后
				'link_before'     => '',//显示在导航链接名之后
				'link_after'      => '',//显示在导航链接名之前
				'items_wrap'      => '<ul id="%1$s">%3$s</ul>',
				'depth'           => 1,////显示的菜单层数，默认0，0是显示所有层
				'walker'          => '')); 

				$str=preg_replace("/<ul[^>]*>/", "", $str,1);
				$str=str_replace("</ul>", "", $str);
				$str=preg_replace("/<li[^>]*>/", "", $str);
				$str=str_replace("</li>", "", $str);
				echo $str;

		?></div>
  </div>
  <?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); setPostViews(get_the_ID());?>
  <?php get_template_part('sidebarPage');?>
  <div  class="right">
    <div  class="article_mod">
      <div  class="title">
        <h1><?php the_title_attribute(); ?></h1>
        <h3> <?php the_author_posts_link(); ?><?php the_category(', ') ?><span  class="date"><?php the_time('Y-m-d H:i:s') ?></span> </h3>
      </div>
      
      <div  class="text tj"  id="content">
      <div style="max-width:635px;overflow:hidden;margin-top:10px;"><script type="text/javascript">
/*自定义标签云，创建于2014-4-26*/
var cpro_id = "u1535272";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script></div>
		<?php the_readerwall(); ?>
     	<?php the_content("Read More..."); ?>
      </div>
      <div  class="data"> <span  class="read">阅读(<strong><?php echo getPostViews(get_the_ID());?>次</strong>)</span> <span  class="commentNum_mod"> <a  href="#comments"  boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;right_blog_reply&#39;, sBak1:&#39;blog&#39;, sBak2:&#39;chenyeyuze&#39;}"><?php comments_number('0', '1', '%' );?></a>  </span> <span  class="like_mod"><span  class="addOne"></span></span> </div>
      
      <div  class="author_mod clear  hidden ">
        <div  class="face"> <a  href="http://dajia.qq.com/user/wugou1975#af"  boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;left_profile_pic&#39;, sBak1:&#39;blog&#39;, sBak2:&#39;chenyeyuze&#39;}"><span><img  src="./content_files/100"  width="102"  height="102"  alt=""></span></a> </div>
        <div  class="name"> <a  href="http://dajia.qq.com/user/wugou1975#af"  boss="{id:1220, sBiz:&#39;dajia_web&#39;, name:&#39;left_profile_name&#39;, sBak1:&#39;blog&#39;, sBak2:&#39;chenyeyuze&#39;}">吴钩</a> </div>
        <div  class="text tj"> 吴钩，历史研究者，推崇传统文化。主要关注宋、明、清社会自治史与儒家学说。著有《隐权力》、《隐权力2》。 </div>
      </div>
      <div  class="articleIndex_mod clear">
        <div  class="moreArticle clear">
          <h2>作者其它文章：</h2>
          <ul>
          <?php
  global $post;
  $post_author = get_the_author_meta( 'user_login' );
  $args = array(
        'author_name' => $post_author,
        'post__not_in' => array($post->ID),
        'showposts' => 6,               // 显示相关文章数量
        'orderby' => date,          // 按时间排序
        'caller_get_posts' => 1
    );
  query_posts($args);
  if (have_posts()) {
    while (have_posts()) {
      the_post(); update_post_caches($posts); ?>
  <li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
<?php
    }
  }
  else {
    echo '<li>* 暂无相关文章</li>';
  }
  wp_reset_query();
?>
          </ul>
        </div>
      </div>
      <div  class="publicationComment"  id="replyPub">
      <div style="max-width:640px;overflow:hidden;">
      <script type="text/javascript">
/*640*60，创建于2014-6-17*/
var cpro_id = "u1590052";
</script>
<script src="http://cpro.baidustatic.com/cpro/ui/c.js" type="text/javascript"></script>
</div>
       <?php comments_template(); ?>
       <?php endwhile; ?>
            
			<?php else : ?>
			<?php endif; ?>
      </div>
      
    </div>
   
<?php get_footer();?>