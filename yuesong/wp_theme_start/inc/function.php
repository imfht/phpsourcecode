<?php 

/*
 *利于seo的wordpress标题
 */
function wp_seo_title(){
	 if ( is_category() ) {
		_e('Category Archive for &quot;','ets'); single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_tag() ) {
		_e('Tag Archive for &quot;','ets'); single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_archive() ) {
		wp_title(''); _e(' Archive | ','ets'); bloginfo( 'name' );
	} elseif ( is_search() ) {
		_e('Search for &quot;','ets').wp_specialchars($s).'&quot; | '; bloginfo( 'name' );
	} elseif ( is_home() ) {
		bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
	}  elseif ( is_404() ) {
		_e('Error 404 Not Found | ','ets'); bloginfo( 'name' );
	} elseif ( is_single() ) {
		wp_title('');
	}else {
		echo wp_title(''); echo ' | '; bloginfo( 'name' );
	}
}

// 摘要
function the_abstract($string,$num){
	echo mb_strimwidth(strip_tags(apply_filters('the_content', $string)), 0, $num,"...");
}

/*
 *当前页面url
 */
function the_url(){
	$pageURL = 'http'; 
    if ($_SERVER["HTTP"] == "on")  
    { 
        $pageURL .= "s"; 
    } 
    $pageURL .= "://"; 
    if ($_SERVER["SERVER_PORT"] != "80")  
    { 
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]; 
    }  
    else  
    { 
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; 
    } 
    return $pageURL; 
}

/*
 *获取文章分类
 */
function the_post_term($taxonomy="category"){
	$terms = get_the_terms( get_the_id(),$taxonomy);
	if($terms){
		foreach ($terms as $key => $term) {
			echo '<a href="'. get_term_link($term) .'">'. $term->name .'</a>  ';
		}
	}
}

/*
 *获取二维码
 */
function the_qrcode_image($url=''){
	if($url==''){
		$url=the_url();
	}?>
<div id="page-erweima"></div>
<script src="<?php bloginfo('template_url'); ?>/style/js/jquery.qrcode.min.js"></script>
<script>
$(function(){
  $('#page-erweima').qrcode({
    width:260,
    height:260,
    text:'<?php echo $url; ?>'
  });
});
</script>
<?php }


/*
 * 分页
 */
function pagenav($range = 9){
	echo '<ul>';
	global $paged, $wp_query;
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){if(!$paged){$paged = 1;}
	echo '<li class="previous">' . get_previous_posts_link(' 上一页 ') . '</li>';
    if($max_page > $range){
		if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
    	elseif($paged >= ($max_page - ceil(($range/2)))){
		for($i = $max_page - $range; $i <= $max_page; $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
		elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
		for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='active'";echo ">$i</a></li>";}}}
    	else{for($i = 1; $i <= $max_page; $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
    if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
    echo '<li class="next">'.get_next_posts_link(' 下一页 ').'</li>';}
	echo '</ul>';
?>
<script>$(".pagination .active").parent().addClass('active');</script>
<?php
}


/*
 *最新文章
 */
function the_last_posts($showposts=5,array $options = array()){
	$args = array_merge($options,array(
		'showposts'	=>$showposts,
		'orderby'	=>'date',
		'order'		=>'desc',
		));
	query_posts($args);
	echo '<ul class="list-group">';
	while(have_posts()) : the_post() ?>
		<li class="list-group-item"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile;
	wp_reset_query();
	echo '</ul>';
}
/*
 *随机文章
 */
function the_random_posts($showposts=10,array $options = array()){
	$args = array_merge($options,array(
		'showposts'	=>$showposts,
		'orderby'	=>'rand',
		));
	query_posts($args);
	echo '<ul class="list-group">';
	while(have_posts()) : the_post() ?>
		<li class="list-group-item"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile;
	wp_reset_query();
	echo '</ul>';
}

/*
 * 相关文章
 */
function the_relation_posts($post_num = 10){
	echo '<ul class="list-group">';
	$exclude_id = get_the_id();
	$posttags = get_the_tags(); $i = 0;
	if ( $posttags ) {
		$tags = ''; foreach ( $posttags as $tag ) $tags .= $tag->term_id . ',';
		$args = array(
			'post_status' => 'publish',
			'tag__in' => explode(',', $tags),
			'post__not_in' => explode(',', $exclude_id),
			'caller_get_posts' => 1,
			'orderby' => 'comment_date',
			'posts_per_page' => $post_num,
		);
		query_posts($args);
		while( have_posts() ) { the_post(); ?>
			<li class="list-group-item"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
		<?php
			$exclude_id .= ',' . $post->ID; $i ++;
		} wp_reset_query();
	}
	if ( $i < $post_num ) {
		$cats = ''; foreach ( get_the_category() as $cat ) $cats .= $cat->cat_ID . ',';
		$args = array(
			'category__in' => explode(',', $cats),
			'post__not_in' => explode(',', $exclude_id),
			'caller_get_posts' => 1,
			'orderby' => 'comment_date',
			'posts_per_page' => $post_num - $i
		);
		query_posts($args);
		while( have_posts() ) { the_post(); ?>
			<li class="list-group-item"><a href="<?php the_permalink(); ?>"  title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
		<?php $i++;
		} wp_reset_query();
	}
	if ( $i  == 0 )  echo '<li class="list-group-item">没有相关文章!</li>';
	echo '</ul>';
}

/*
 *bootsrap导航
 */
//bootstrap 的导航栏Walker
require get_template_directory() . '/inc/class/WP_Bootstrap_Nav.php';
function wp_bootstrap_nav_menu($menuname, $option = array()){
	$args = array_merge(array(
		'theme_location' => $menuname,
		'container'       => 'ul',
		'menu_class' => 'nav navbar-nav',
		// 'walker' => new WP_Bootstrap_Nav(),
		),$option);
	wp_nav_menu( $args );
}

/*
 * json rest api支持自定义字段
 */
function json_api_prepare_post( $post_response, $post, $context ) {
  $field = get_post_custom($post['ID']);
  $post_response['custom-fields'] = $field;
  return $post_response;
}
add_filter( 'json_prepare_post', 'json_api_prepare_post', 10, 3 );
