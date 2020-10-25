<?php
//禁用历史版本
remove_action('pre_post_update', 'wp_save_post_revision' );
//导航菜单
register_nav_menus(array('header-menu' => __( 'Damien-Menu' ),));  
add_theme_support('nav-menus');  
if(function_exists('register_nav_menus')){  
    register_nav_menus(  
        array(  
           
	'one-menu' => __( '第一栏自定义菜单' ),
        )  
    );  
}  

function tedlife_menu(){  
    if(function_exists('wp_nav_menu') && has_nav_menu('header_menu')):    
        wp_nav_menu(      
            array(      
                'menu'              => 'Header Navigation',    
                'container'         => 'div',    
                'container_class'   => '',    
                'container_id'      => '',    
                'menu_class'        => '',    
                'menu_id'           => '',    
                'echo'              => true,    
                'fallback_cb'       => 'fallback_no_menu',    
                'before'            => '',    
                'after'             => '',    
                'link_before'       => '<li><span>',    
                'link_after'        => '</span></li>',    
                'depth'             => 2,    
                'walker'            => new Walker_Nav_Menu(),    
                'theme_location'    => '',    
                'show_home'         => true    
        )  
    );  
    endif;  
} 
//统计浏览次数
function getPostViews($postID){
  $count_key = 'post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if($count==''){
	  delete_post_meta($postID, $count_key);
	  add_post_meta($postID, $count_key, '0');
	  return 0;
  }
  return $count;
}

function setPostViews($postID) {
  $count_key = 'post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if($count==''){
	  $count = 0;
	  delete_post_meta($postID, $count_key);
	  add_post_meta($postID, $count_key, '0');
  }else{
	  $count++;
	  update_post_meta($postID, $count_key, $count);
  }
}

//分页
function pagenavi( $before = '', $after = '', $p = 2 ) {   
if ( is_singular() ) return;   
global $wp_query, $paged;   
$max_page = $wp_query->max_num_pages;   
if ( $max_page == 1 ) return;   
if ( empty( $paged ) ) $paged = 1;   
echo $before.'<div class="wp-pagenavi">'."\n";   
echo '<span class="pages">' . $paged . ' /' . $max_page . ' </span>';   
if ( $paged > 1 ) p_link( $paged - 1, '上一页', '«' );   
if ( $paged > $p + 1 ) p_link( 1, '第一页' );   
if ( $paged > $p + 2 ) echo ' ';   
for( $i = $paged - $p; $i <= $paged + $p; $i++ ) {   
if ( $i > 0 && $i <= $max_page ) $i == $paged ? print "<span class='current'>{$i}</span>" : p_link( $i );   
}   
if ( $paged < $max_page - $p - 1 ) echo '... ';   
if ( $paged < $max_page - $p ) p_link( $max_page, '最后一页' );   
if ( $paged < $max_page ) p_link( $paged + 1,'下一页', '»' );   
echo '</div>'.$after."\n";   
}   
function p_link( $i, $title = '', $linktype = '' ) {   
if ( $title == '' ) $title = "第 {$i} 页";   
if ( $linktype == '' ) { $linktext = $i; } else { $linktext = $linktype; }   
echo "<a  href='", esc_html( get_pagenum_link( $i ) ), "' title='{$title}'>{$linktext}</a>";   
} 

function xiayiye( $before = '', $after = '', $p = 2 ) {   
if ( is_singular() ) return;   
global $wp_query, $paged;   
$max_page = $wp_query->max_num_pages;   
if ( $max_page == 1 ) return;   
if ( empty( $paged ) ) $paged = 1;   
echo $before.'<div class="page"><ul>'."\n";   
//echo '<span class="pages">Page: ' . $paged . ' of ' . $max_page . ' </span>';   
if ( $paged > 1 ) p_link( $paged - 1, '上一页', '上一页' );   
//if ( $paged > $p + 2 ) echo ' ';   

//if ( $paged < $max_page - $p - 1 ) echo '... ';   
if ( $paged < $max_page ) p_link( $paged + 1,'下一页', '下一页' );   
echo '</ul></div>'.$after."\n";   
}   


//特色图像
if ( function_exists( 'add_theme_support' ) ) {     add_theme_support( 'post-thumbnails' ); } if ( function_exists( 'add_image_size' ) ) {     add_image_size( 'customized-post-thumb', 192, 120 ); } 

//小工具
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<div class="widget">	',
        'after_widget' => '</div>',
        'before_title' => '<div class="widget-title"><h3>',
        'after_title' => '</h3></div>',
    ));

//面包屑
function fairy_breadcrumbs() {
    $delimiter = '&raquo;';
      $home = '首页'; // text for the 'Home' link
      $before = '<span class="current">'; // tag before the current crumb
      $after = '</span>'; // tag after the current crumb
      if ( !is_home() && !is_front_page() || is_paged() ) {
        echo '<div id="crumbs">';
        global $post;
        $homeLink = get_bloginfo('url');
        echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
     if ( is_category() ) {
          global $wp_query;
          $cat_obj = $wp_query->get_queried_object();
         $thisCat = $cat_obj->term_id;
          $thisCat = get_category($thisCat);
          $parentCat = get_category($thisCat->parent);
          if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
              echo $before . ' "' . single_cat_title('', false) . '" 目录下的文章' . $after;
    } else if ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
        echo $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        echo $before . get_the_title() . $after;
      }
    } else if ( !is_single() && !is_page() && get_post_type() != 'post' ) {
          $post_type = get_post_type_object(get_post_type());
          echo $before . $post_type->labels->singular_name . $after;
    }
    if ( get_query_var('paged') ) {
          if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
          echo __('Page') . ' ' . get_query_var('paged');
          if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
    echo '</div>';
 }
}
//启用友情链接插件
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
//显示NEW文章
function add_title_icon()
{
    global $post;
    $post_date=$post->post_date;
    $current_time=current_time('timestamp');
    $diff=($current_time-strtotime($post_date))/3600;
    $title_icon_new=get_bloginfo('template_directory').'/images/title_icon/new.gif';
    if($diff<24)
    {
    $title='<i class="shareIcon" style="display:block;"></i>';
    }else
	{
		$title='<i class="shareIcon"></i>';
	}
    return $title;
}



function diy_most_comments($num = 10, $daynum = 30) {
	global $wpdb;
	$now = gmdate("Y-m-d H:i:s",time());
	$popularposts = "SELECT ID, post_title, post_date, comment_count, COUNT($wpdb->comments.comment_post_ID) AS 'popular' FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' AND post_date > date_sub( NOW(), INTERVAL $daynum DAY ) AND comment_status = 'open' GROUP BY $wpdb->comments.comment_post_ID ORDER BY popular DESC LIMIT $num";
	$posts = $wpdb->get_results($popularposts);
	$popular = '';
	if($posts){
	foreach($posts as $post){
	$post_title = stripslashes($post->post_title);
	$post_date = stripslashes($post->post_date);
	$comments = stripslashes($post->comment_count);
	$guid = get_permalink($post->ID);
	$popular .= '<li><a href="'.$guid.'#comments" title="'.$post_title.'">'.$post_title.' <span>('.$comments.')</span></a></li>';
	}	
}else 
{
	$popular = '<li>coming soon...</li>'."\n";
}
	echo $popular;
};

function auto_keywords() {

  global $s, $post;
  $keywords = '';
  if ( is_single() ) {
    if ( get_the_tags( $post->ID ) ) {
      foreach ( get_the_tags( $post->ID ) as $tag ) $keywords .= $tag->name . ', ';
    }
    foreach ( get_the_category( $post->ID ) as $category ) $keywords .= $category->cat_name . ', ';
    $keywords = substr_replace( $keywords , '' , -2);
  } elseif ( is_home () )    { $keywords = get_option('T_keywords');
  } elseif ( is_tag() )      { $keywords = single_tag_title('', false);
  } elseif ( is_category() ) { $keywords = single_cat_title('', false);
  } elseif ( is_search() )   { $keywords = esc_html( $s, 1 );
  } else { $keywords = trim( wp_title('', false) );
  }
  if ( $keywords ) {
    echo "<meta name=\"keywords\" content=\"$keywords\" />\n";
  }
}
function auto_description() {
  global $s, $post;
  $description = '';
  $blog_name = get_bloginfo('name');
  if ( is_singular() ) {
    if( !empty( $post->post_excerpt ) ) {
      $text = $post->post_excerpt;
    } else {
      $text = $post->post_content;
    }
    $description = trim( str_replace( array( "\r\n", "\r", "\n", "　", " "), " ", str_replace( "\"", "'", strip_tags( $text ) ) ) );
    if ( !( $description ) ) $description = $blog_name . " - " . trim( wp_title('', false) );
  } elseif ( is_home () )    { $description = $blog_name . " - " . get_option('T_description'); // 首頁要自己加
  } elseif ( is_tag() )      { $description = $blog_name . "有关 '" . single_tag_title('', false) . "' 的文章";
  } elseif ( is_category() ) { $description = $blog_name . "有关 '" . single_cat_title('', false) . "' 的文章";
  } elseif ( is_archive() )  { $description = $blog_name . "在: '" . trim( wp_title('', false) ) . "' 的文章";
  } elseif ( is_search() )   { $description = $blog_name . ": '" . esc_html( $s, 1 ) . "' 的搜索結果";
  } else { $description = $blog_name . "有关 '" . trim( wp_title('', false) ) . "' 的文章";
  }
  $description = mb_substr( $description, 0, 220, 'utf-8' ) . '..';
  echo "<meta name=\"description\" content=\"$description\" />\n";
}



 //标签云滤镜
add_filter( 'widget_tag_cloud_args', 'theme_tag_cloud_args' );
function theme_tag_cloud_args( $args ){
if(get_option('themes_fo2_tag_num')){$tagNum = get_option('themes_fo2_tag_num');}else{$tagNum = 45;}
$newargs = array(
'smallest'    => 14,  //最小字号
'largest'     => 14, //最大字号
'unit'        => 'px',   //字号单位，可以是pt、px、em或%
'number'      => $tagNum,     //显示个数
'format'      => 'flat',//列表格式，可以是flat、list或array
'separator'   => "\n",   //分隔每一项的分隔符
'orderby'     => 'count',//排序字段，可以是name或count
'order'       => 'DESC', //升序或降序，ASC或DESC
'exclude'     => null,   //结果中排除某些标签
'include'     => null,  //结果中只包含这些标签
'link'        => 'view', //taxonomy链接，view或edit
'taxonomy'    => 'post_tag', //调用哪些分类法作为标签云
);
$return = array_merge( $args, $newargs);
return $return;
}

//读者墙
function zsofa_most_active_friends($friends_num = 10) {
    global $wpdb;
    $counts = $wpdb->get_results("SELECT COUNT(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email FROM (SELECT * FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID=$wpdb->comments.comment_post_ID) WHERE comment_date > date_sub( NOW(), INTERVAL 1 MONTH ) AND user_id='0' AND comment_author != 'zwwooooo' AND post_password='' AND comment_approved='1' AND comment_type='') AS tempcmt GROUP BY comment_author ORDER BY cnt DESC LIMIT $friends_num");
    foreach ($counts as $count) {
    $c_url = $count->comment_author_url;
    if ($c_url == '') $c_url = get_bloginfo('url');
    $mostactive .= '<li class="read_li">' . '<a href="'. $c_url . '" title="' . $count->comment_author . ' ('. $count->cnt . '条评论)">' . get_avatar($count->comment_author_email, 32) . '</a></li>';
    }
    echo $mostactive;
}

//禁止后台升级
add_filter( 'automatic_updater_disabled','__return_true' );  

//判断是否最新文章
function is_new($time,$days=1){
	if((time() - $time) < (3600 * 24 * $days)){
		return true;
	}else{
		return false;
	}
}   

add_action('admin_menu', 'mytheme_page');
function mytheme_page (){
	if ( count($_POST) > 0 && isset($_POST['mytheme_settings']) ){
		$options = array ('keywords','description','analytics','content_right','comment_top','meun_bottom','info_bottom','bottom_link','starttime');
		foreach ( $options as $opt ){
			delete_option ( 'mytheme_'.$opt, $_POST[$opt] );
			add_option ( 'mytheme_'.$opt, $_POST[$opt] );	
		}
	}
	add_theme_page(__('主题选项'), __('Clmao主题选项'), 'edit_themes', basename(__FILE__), 'mytheme_settings');
}
function mytheme_settings(){?>
<style>
	.wrap,textarea,em{font-family:'Century Gothic','Microsoft YaHei',Verdana;}
	fieldset{width:100%;border:1px solid #aaa;padding-bottom:20px;margin-top:20px;-webkit-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 5px;box-shadow:rgba(0,0,0,.2) 0px 0px 5px;}
	legend{margin-left:5px;padding:0 5px;color:#2481C6;background:#F9F9F9;cursor:pointer;}
	textarea{width:100%;font-size:11px;border:1px solid #aaa;background:none;-webkit-box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;-moz-box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;box-shadow:rgba(0,0,0,.2) 1px 1px 2px inset;-webkit-transition:all .4s ease-out;-moz-transition:all .4s ease-out;}
	textarea:focus{-webkit-box-shadow:rgba(0,0,0,.2) 0px 0px 8px;-moz-box-shadow:rgba(0,0,0,.2) 0px 0px 8px;box-shadow:rgba(0,0,0,.2) 0px 0px 8px;outline:none;}
</style>
<div class="wrap">
<h2>Clmao仿腾讯大家主题选项</h2>作者博客：<a href="http://blog.clmao.com" target="_blank">撒哈拉的小猫</a><br/>在撰写文章需要插入代码请用&lt;pre class="brush:php;toolbar:false"&gt;&lt;/pre&gt;标签包裹，其中php修改成你的代码语言即可<br/>LOGO和ICO直接覆盖主题目录下的logo.png和favicon.ico即可
<form method="post" action="">
	<fieldset>
	<legend><strong>SEO选项</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="keywords" id="keywords" rows="1" cols="70"><?php echo get_option('mytheme_keywords'); ?></textarea><br />
				<em>网站关键词（Meta Keywords），中间用半角逗号隔开。</em>
			</td></tr>
			<tr><td>
				<textarea name="description" id="description" rows="3" cols="70"><?php echo get_option('mytheme_description'); ?></textarea>
				<em>网站描述（Meta Description），针对搜索引擎设置的网页描述。</em>
			</td></tr>
		</table>
	</fieldset>
 
	<fieldset>
	<legend><strong>统计代码添加</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="analytics" id="analytics" rows="5" cols="70"><?php echo stripslashes(get_option('mytheme_analytics')); ?></textarea>
			</td></tr>
		</table>
	</fieldset>

	<fieldset>
	<legend><strong>广告代码添加</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="content_right" id="content_right" rows="3" cols="70"><?php echo get_option('mytheme_content_right'); ?></textarea><br />
				<em>文章页面右边广告，宽度建议360PX</em>
			</td></tr>
			<tr><td>
				<textarea name="comment_top" id="comment_top" rows="3" cols="70"><?php echo get_option('mytheme_comment_top'); ?></textarea>
				<em>评论上边广告，宽度建议640PX</em>
			</td></tr>
			<tr><td>
				<textarea name="meun_bottom" id="meun_bottom" rows="3" cols="70"><?php echo get_option('mytheme_meun_bottom'); ?></textarea>
				<em>导航下边广告，宽度建议640PX</em>
			</td></tr>
			<tr><td>
				<textarea name="info_bottom" id="info_bottom" rows="3" cols="70"><?php echo get_option('mytheme_info_bottom'); ?></textarea>
				<em>博客信息下边广告，宽度建议400PX</em>
			</td></tr>
		</table>
	</fieldset>

	<fieldset>
	<legend><strong>底部链接代码添加</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="bottom_link" id="bottom_link" rows="5" cols="70"><?php echo stripslashes(get_option('mytheme_bottom_link')); ?></textarea>
			</td></tr>
		</table>
	</fieldset>

	<fieldset>
	<legend><strong>其他</strong></legend>
		<table class="form-table">
			<tr><td>
				<textarea name="starttime" id="starttime" rows="1" cols="70"><?php echo stripslashes(get_option('mytheme_starttime')); ?></textarea>
				<em>博客开始时间，格式： 2014-1-1</em>
			</td></tr>
		</table>
	</fieldset>
 
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="保存设置" />
		<input type="hidden" name="mytheme_settings" value="save" style="display:none;" />
	</p>
</form>
</div>
<?php }
?>