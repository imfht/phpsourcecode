<?php
/**
 * @package WordPress
 * @subpackage Theme_Compat
 * @deprecated 3.0
 *
 * This file is here for Backwards compatibility with old themes and will be removed in a future version
 *
 */
_deprecated_file( sprintf( __( 'Theme without %1$s' ), basename(__FILE__) ), '3.0', null, sprintf( __('Please include a %1$s template in your theme.'), basename(__FILE__) ) );

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.'); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>
	<h3 id="comments" class="comments-header">文章 "<?php echo get_the_title(); ?>" 有 <?php echo number_format_i18n( get_comments_number() ) ?> 条评论<small class="add-commentbtn"><a class="btn btn-primary btn-sm" href="#commentform">添加新评论</a></small></h3>
	<div class="clearfix"></div>
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>

	<div class="commentlist">
	<?php wp_list_comments(array('style'=>'div','callback'=>'eyas_comment','avatar_size'=>100));?>
	</div>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e('Comments are closed.'); ?></p>

	<?php endif; ?>
<?php endif; ?>

<?php if ( comments_open() ) : ?>

<div id="respond">

<h3><?php comment_form_title( __('Leave a Reply'), __('Leave a Reply to %s' ) ); ?></h3>

<div id="cancel-comment-reply">
	<small><?php cancel_comment_reply_link() ?></small>
</div>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url( get_permalink() )); ?></p>
<?php else : ?>

<form action="<?php echo site_url(); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( is_user_logged_in() ) : ?>

<p><?php printf(__('Logged in as <a href="%1$s">%2$s</a>.'), get_edit_user_link(), $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php esc_attr_e('Log out of this account'); ?>"><?php _e('Log out &raquo;'); ?></a></p>

<?php else : ?>

<div class="form-group">
	<label for="author">昵称 <?php if ($req) _e('(required)'); ?></label>
	<input name="author" type="text" class="form-control" id="author" value="<?php echo esc_attr($comment_author); ?>" placeholder="请输入昵称" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?>>
</div>
<div class="form-group">
	<label for="email"><?php _e('Mail (will not be published)'); ?> <?php if ($req) _e('(required)'); ?></label>
	<input name="email" type="email" class="form-control" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> placeholder="请输入电子邮箱">
</div>
<div class="form-group">
	<label for="url"><?php _e('Website'); ?></label>
	<input name="url" type="text" class="form-control" id="url" value="<?php echo  esc_attr($comment_author_url); ?>" size="22" tabindex="3" placeholder="请输入您的站点url">
</div>
<?php endif; ?>

<!--<p><small><?php printf(__('<strong>XHTML:</strong> You can use these tags: <code>%s</code>'), allowed_tags()); ?></small></p>-->

<p><textarea name="comment" placeholder="请输入评论内容" class="form-control" id="comment" rows="5" tabindex="4"></textarea></p>

<p><input name="submit" class="btn btn-primary hidden-xs" type="submit" id="submit" tabindex="5" value="<?php esc_attr_e('Submit Comment'); ?>" />
   <input name="submit" class="btn btn-primary visible-xs btn-block" type="submit" id="submit" tabindex="5" value="<?php esc_attr_e('Submit Comment'); ?>" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>