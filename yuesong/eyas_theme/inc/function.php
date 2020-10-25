<?php 
// page nav
function pagination($query_string){
	global $posts_per_page, $paged;
	$my_query = new WP_Query($query_string ."&posts_per_page=-1");
	$total_posts = $my_query->post_count;
	if(empty($paged))$paged = 1;
	$prev = $paged - 1;							
	$next = $paged + 1;	
	$range = 5; // 分页数设置
	$showitems = ($range * 2)+1;
	$pages = ceil($total_posts/$posts_per_page);
	if(1 != $pages){
		echo "<ul>";
		echo "<li class='previous'><a href='".get_pagenum_link($prev)."'><span class='glyphicon glyphicon-chevron-left'></span></a></li>\n";		
		for ($i=1; $i <= $pages; $i++){
		if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
		echo ($paged == $i)? "<li class='active'><a href='#'>".$i."</a></li>\n":"<li><a href='".get_pagenum_link($i)."' >".$i."</a></li>\n"; 
		}
		}
		echo "<li class='next'><a href='".get_pagenum_link($next)."'><span class='glyphicon glyphicon-chevron-right'></span></a></li>\n";
		echo "</ul>\n";
	}
}
// 列出分类目录
function the_category_list(){
	$args = array(
		'type'                     => 'post',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 1,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'category',
		'pad_counts'               => false 
	);
	echo '<ul class="list-group">';
	foreach(get_categories($args) as $cate){
		echo '<li class="list-group-item"><a href="'.get_category_link( $cate->term_id ).'">'.$cate->name.'<span> ('.$cate->category_count.')</span></a></li>';
	}
	echo '</ul>';
}
// 最新文章
function the_laest_post(){
	query_posts('showposts=5&orderby=date');
	echo '<ul class="list-group">';
	while(have_posts()) : the_post();?>
	<li class="list-group-item"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile;
	wp_reset_query();
	echo '</ul>';
}
// 访问最多
function the_most_postviews($mode = '', $limit = 10, $chars = 0, $display = true){
	echo '<ul class="list-group">';
	global $wpdb;
	$views_options = get_option('views_options');
	$where = '';
	$temp = '';
	$output = '';
	if(!empty($mode) && $mode != 'both') {
		if(is_array($mode)) {
			$mode = implode("','",$mode);
			$where = "post_type IN ('".$mode."')";
		} else {
			$where = "post_type = '$mode'";
		}
	} else {
		$where = '1=1';
	}
	$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER BY views DESC LIMIT $limit");
	if($most_viewed) {
		foreach ($most_viewed as $post):?>
			<li class="list-group-item"><a href="<?php echo post_permalink( $post->ID ); ?>"><?php echo $post->post_title; ?></a><span>(浏览 <?php echo $post->views; ?> 次)</span></li>
		<?php endforeach;
	} else {
		$output = '<li class="list-group-item">'.__('N/A', 'wp-postviews').'</li>'."\n";
	}
	if($display) {
		echo $output;
	} else {
		return $output;
	}
	echo '</ul>';
}
// 随机文章
function the_random_posts(){
	$args = array( 'showposts' => 5, 'orderby' => 'rand', 'post_status' => 'publish' );
	query_posts($args);
	echo '<ul class="list-group">';
	while(have_posts()) : the_post();?>
	<li class="list-group-item"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile;
	wp_reset_query();
	echo '</ul>';
}
// 页面二维码
function the_page_qrcode(){

}
// main menu
function the_main_menu(){
	   /**
		* Displays a navigation menu
		* @param array $args Arguments
		*/
		$args = array(
			'theme_location' => 'main_menu',
			'container' => 'ul',
			'menu_class' => 'nav navbar-nav',
		);
		wp_nav_menu( $args );
}