<?php
// 近期留言
class recent_comments extends WP_Widget {
     function recent_comments() {
         $widget_ops = array('description' => '主题自带的近期留言小工具');
         $this->WP_Widget('recent_comments', '主题&nbsp;&nbsp;近期留言', $widget_ops);
     }
     function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
?>

<div id="message" class="panel-body message-widget">
	<ul>
		<?php
		$show_comments = $number;
		$my_email = get_bloginfo ('admin_email');
		$i = 1;
		$comments = get_comments('number=200&status=approve&type=comment');
		foreach ($comments as $my_comment) {
			if ($my_comment->comment_author_email != $my_email) {
				?>
				<li>
					<a href="<?php echo get_permalink($my_comment->comment_post_ID); ?>#comment-<?php echo $my_comment->comment_ID; ?>" title="发表在 > <?php echo get_the_title($my_comment->comment_post_ID); ?>" >
						<?php echo get_avatar($my_comment->comment_author_email,48); ?>
						<span class="comment_author"><strong><?php echo $my_comment->comment_author; ?></strong></span>
						<?php echo convert_smilies($my_comment->comment_content); ?>
					</a>
				</li>
				<?php
				if ($i == $show_comments) break;
				$i++;
			}
		}
		?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
			$instance = $old_instance;
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags($new_instance['number']);
			return $instance;
		}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '近期评论';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "recent_comments" );' ) );

// 热门文章
class hot_post extends WP_Widget {
     function hot_post() {
         $widget_ops = array('description' => '主题自带的热门文章小工具，使用前必须安装 wp-postviews 插件,并且有统计数据');
         $this->WP_Widget('hot_post', '主题&nbsp;&nbsp;热门文章', $widget_ops);
     }
     function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		$days = strip_tags($instance['days']) ? absint( $instance['days'] ) : 90;
?>

<div id="hot_post_widget" class="panel-body">
	<ul>
	    <?php if (function_exists('get_most_viewed')): ?> 
	    <?php get_timespan_most_viewed('post',$number,$days, true, true); ?>
	    <?php endif; ?>
		<?php wp_reset_query(); ?>
	</ul>
</div>
<script type="text/javascript">
function setSN(e){
	var ul = document.getElementById(e);
	var lis= ul.getElementsByTagName('li');
		for(var i=0,l=lis.length;i<3;i++){
			var tHTML = lis[i].innerHTML
			lis[i].innerHTML = '<span id="li-icon" class="ago-icon">'+(i+1)+'</span>'+tHTML;
		}
		for(var i=3,l=lis.length;i<l;i++){
			var tHTML = lis[i].innerHTML
			lis[i].innerHTML = '<span id="li-icon" class="after-icon">'+(i+1)+'</span>'+tHTML;
		}
}
setSN('hot_post_widget');
</script>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
			$instance = $old_instance;
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags($new_instance['number']);
			$instance['days'] = strip_tags($new_instance['days']);
			return $instance;
		}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '热门文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$instance = wp_parse_args((array) $instance, array('days' => '90'));
		$number = strip_tags($instance['number']);
		$days = strip_tags($instance['days']);
 ?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
	<p><label for="<?php echo $this->get_field_id('days'); ?>">时间限定（天）：</label>
	<input id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo $days; ?>" size="3" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "hot_post" );' ) );

// 热评文章
class hot_comment extends WP_Widget {
     function hot_comment() {
         $widget_ops = array('description' => '主题自带的热评文章小工具');
         $this->WP_Widget('hot_comment', '主题&nbsp;&nbsp;热评文章', $widget_ops);
     }
     function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
		$days = strip_tags($instance['days']) ? absint( $instance['days'] ) : 90;
?>

<?php
function hot_comment_viewed($number, $days){
    global $wpdb;
    $sql = "SELECT ID , post_title , comment_count
           FROM $wpdb->posts
           WHERE post_type = 'post' AND post_status = 'publish' AND TO_DAYS(now()) - TO_DAYS(post_date) < $days
           ORDER BY comment_count DESC LIMIT 0 , $number ";
    $posts = $wpdb->get_results($sql);
	$ico = '<i class="icon-li"></i>';
    $output = "";
    foreach ($posts as $post){
        $output .= "\n<li>".$ico."<a href= \"".get_permalink($post->ID)."\" rel=\"bookmark\" title=\"".$post->post_title." (".$post->comment_count."条评论)\" >".$post->post_title."</a></li>";
    }
    echo $output;
}
?>
<div id="hot_comment_widget" class="panel-body">
	<ul>
		<?php hot_comment_viewed($number, $days); ?>
		<?php wp_reset_query(); ?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
			$instance = $old_instance;
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags($new_instance['number']);
			$instance['days'] = strip_tags($new_instance['days']);
			return $instance;
		}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '热评文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$instance = wp_parse_args((array) $instance, array('days' => '90'));
		$number = strip_tags($instance['number']);
		$days = strip_tags($instance['days']);
 ?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
	<p><label for="<?php echo $this->get_field_id('days'); ?>">时间限定（天）：</label>
	<input id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo $days; ?>" size="3" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "hot_comment" );' ) );

// 随机文章
class random_post extends WP_Widget {
     function random_post() {
         $widget_ops = array('description' => '主题自带的随机文章小工具');
         $this->WP_Widget('random_post', '主题&nbsp;&nbsp;随机文章', $widget_ops);
     }
     function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
?>

<div id="random_post_widget" class="panel-body">
	<ul  class="list-group">
		<?php query_posts( array ( 'orderby' => 'rand', 'showposts' => $number, 'caller_get_posts' => 10 ) ); while ( have_posts() ) : the_post(); ?>
			<li class="list-group-item"><a href="<?php the_permalink() ?>" rel="bookmark" title="详细阅读 <?php the_title(); ?>"><i class="icon-li"></i><?php the_title(); ?></a></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
			$instance = $old_instance;
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags($new_instance['number']);
			return $instance;
		}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '随机文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "random_post" );' ) );

// 相关文章
class related_post extends WP_Widget {
     function related_post() {
         $widget_ops = array('description' => '主题自带的相关文章小工具，主要用于正文下方的小工具');
         $this->WP_Widget('related_post', '主题&nbsp;&nbsp;相关文章', $widget_ops);
     }
     function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
		echo $before_title . $title . $after_title;
		$number = strip_tags($instance['number']) ? absint( $instance['number'] ) : 5;
?>

<div id="related_post_widget"  class="panel-body">
	<ul class="list-group">
		<?php
			$post_num = $number;
			global $post;
			$tmp_post = $post;
			$tags = ''; $i = 0;
			if ( get_the_tags( $post->ID ) ) {
			foreach ( get_the_tags( $post->ID ) as $tag ) $tags .= $tag->name . ',';
			$tags = strtr(rtrim($tags, ','), ' ', '-');
			$myposts = get_posts('numberposts='.$post_num.'&tag='.$tags.'&exclude='.$post->ID);
			foreach($myposts as $post) {
			setup_postdata($post);
		?>
		<li class="list-group-item"><a href="<?php the_permalink(); ?>"><i class="icon-li"></i><?php the_title(); ?></a></li>
		<?php
			$i += 1;
			}
			}
			if ( $i < $post_num ) {
			$post = $tmp_post; setup_postdata($post);
			$cats = ''; $post_num -= $i;
			foreach ( get_the_category( $post->ID ) as $cat ) $cats .= $cat->cat_ID . ',';
			$cats = strtr(rtrim($cats, ','), ' ', '-');
			$myposts = get_posts('numberposts='.$post_num.'&category='.$cats.'&exclude='.$post->ID);
			foreach($myposts as $post) {
			setup_postdata($post);
		?>
		<li><i class="icon-li"></i><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
		<?php
		}
		}
		$post = $tmp_post; setup_postdata($post);
		?>
	</ul>
</div>

<?php
	echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
			$instance = $old_instance;
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags($new_instance['number']);
			return $instance;
		}
	function form($instance) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '相关文章';
		}
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('number' => '5'));
		$number = strip_tags($instance['number']);
?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('number'); ?>">显示数量：</label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
	<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "related_post" );' ) );