<?php 
/*
 *利于seo的wordpress标题
 */
if(!function_exists('ey_seo_title')):
function ey_seo_title(){
	 if ( is_category() ) {
		single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_tag() ) {
		 single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_archive() ) {
		wp_title(''); _e('  | ','ey'); bloginfo( 'name' );
	} elseif ( is_search() ) {
		_e('Search for &quot;','ey').wp_specialchars($s).'&quot; | '; bloginfo( 'name' );
	} elseif ( is_home() ) {
		bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
	}  elseif ( is_404() ) {
		_e('Error 404 Not Found | ','ey'); bloginfo( 'name' );
	} elseif ( is_single() ) {
		wp_title('');
	}else {
		echo wp_title(''); echo ' | '; bloginfo( 'name' );
	}
}
endif;


/*
 * 获取摘要 ey_get_abstract($string,$num)
 */
if(!function_exists('ey_get_abstract')):
function ey_get_abstract($string,$num){
	return mb_strimwidth(strip_tags(apply_filters('the_content', $string)), 0, $num,"...");
}
endif;
/*
 *当前页面url
 */
if(!function_exists('ey_current_url')):
function ey_current_url(){
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
endif;

/*
 *获取文章分类 ey_get_the_terms($taxonomy="category")
 */
if(!function_exists('ey_get_the_terms')):
function ey_get_the_terms($taxonomy="category"){
	$terms = get_the_terms( get_the_id(),$taxonomy);
	if($terms){
		return $terms;
	}else{
		return __('No Term','ey');
	}
}
endif;
/*
 * 输出文章分类
 */
if(!function_exists('ey_the_terms_withlink')):
function ey_the_terms_withlink($taxonomy="category"){
	$terms = get_the_terms( get_the_id(),$taxonomy);
	if($terms){
		foreach ($terms as $key => $term) {
			echo '<a href="'. get_term_link($term) .'" title="'.$term->name.'" >'. $term->name .'</a>  ';
		}
	}else{
		echo "<a>".__('No Term','ey').'</a>';
	}
}
endif;
/*
 * 分页
 */
if(!function_exists('ey_pagenav')):
function ey_pagenav($range = 999){
	echo '<ul class="pagination">';
	global $paged, $wp_query;
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){if(!$paged){$paged = 1;}
	echo '<li class="previous">' . get_previous_posts_link(__('<i class="icon-left"></i>','ey')) . '</li>';
	echo '<li><span class="page-number">'.$paged .'/'. $max_page.'</span></li>';
  //   if($max_page > $range){
		// if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
		// if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
  //   	elseif($paged >= ($max_page - ceil(($range/2)))){
		// for($i = $max_page - $range; $i <= $max_page; $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
		// if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
		// elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
		// for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='active'";echo ">$i</a></li>";}}}
    	// else{for($i = 1; $i <= $max_page; $i++){echo "<li><a href='" . get_pagenum_link($i) ."'";
    // if($i==$paged)echo " class='active'";echo ">$i</a></li>";}}
    echo '<li class="next">'.get_next_posts_link(__('<i class="icon-right"></i>','ey')).'</li>';}
	echo '</ul>';
}
endif;

/*
 * 输出菜单
 */
if(! function_exists('ey_the_menu') ):
function ey_the_menu($menu_name,$option = array()){
		$args = array_merge(array(
			'theme_location' => $menu_name,
			'menu' => '',
			'container' => 'ul',
			'menu_class' => 'nav navbar-nav',
			'fallback_cb' => 'ey_menu_fallback',
		),$option);
		ey_wp_nav_menu_cache( $args );
}
function ey_menu_fallback(){
	echo '<ul class="nav navbar-nav"><li>
	<a href="'.home_url('/wp-admin/nav-menus.php').'">请到管理后台：外观->菜单 设置对应的菜单</a>
	</li></ul>';
}
endif;

/*
 * 获取分类法分类列表
 */
if((!function_exists('ey_get_terms'))&&(!function_exists('ey_the_terms'))):
function ey_get_terms($taxonomy='category',$option=array()){
	$args = array(
	    'hide_empty'    => false, //是否隐藏空的分类
	); 
	$terms = get_terms($taxonomy,array_merge($args,$option));
	if($terms){
	    return get_terms($taxonomy,array_merge($args,$option));
	}else{
		return false;
	}
}
function ey_the_terms($taxonomy='category',$option=array()){
	$args = array(
	    'hide_empty'    => false, //是否隐藏空的分类
	); 
	$terms = get_terms($taxonomy,array_merge($args,$option));
    if($terms){
	    foreach ($terms as $key => $term) {
			echo '<a href="'. get_term_link($term) .'" title="'.$term->name.'" >'. $term->name .'</a>  ';
		}
	}else{
		echo "<a>".__('No Term','ey').'</a>';
	}
}
endif;

/*
 * 在分类列表获取当前的分类
 */
if(!function_exists('ey_current_term')):
function ey_current_term(){
	if(is_tax()){
		return get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	}else{
		return false;
	}
}
endif;

/*
 * 面包屑插件
 */
if(!function_exists('ey_breadcrumb')):
function ey_breadcrumb($setting=array()){
	if(!function_exists('show_full_breadcrumb')){
		echo '请安装插件: <a href="https://wordpress.org/plugins/full-breadcrumb/">full breadcrumb</a>';
	}else{
		show_full_breadcrumb($setting);
	}
}
endif;

/*
 * 显示缩略图，如果设置了特色图像，则显示特色图像，如果没有设置，显示文章中第一张图片，如果文章没图片，可在参数中设置默认图片
 */
if(!function_exists('ey_thumbnail')&&!function_exists('ey_get_content_first_image')):
function ey_get_content_first_image($content=false){
	if ( $content === false ) $content = get_the_content(); 

	preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $images);

	if($images){       
		return $images[1][0];
	}else{
		return false;
	}
}
function ey_thumbnail($default=false){
	if(has_post_thumbnail()){
		the_post_thumbnail();
	}elseif(ey_get_content_first_image()){
		echo "<img src='".ey_get_content_first_image()."'>";
	}elseif($default){
		echo "<img src='".$default."'>";
	}else{
		return false;
	}
}
endif;