<div class="blog-post">
  <h2 class="blog-post-title"><?php echo $this->title; ?></h2>
  <p class="blog-post-meta"><?php echo $this->dt_created; ?> by <?php echo $this->creator_name; ?></p>
  <p><?php echo $this->content; ?></p>
  <hr>
</div><!-- /.blog-post -->

<ul class="pagination">
<?php if ($this->prev && is_array($this->prev)) : ?>
  <li><a href="<?php echo $this->urlHelper->getPostView($this->prev); ?>">上一篇：<small><?php echo $this->prev['title']; ?></small></a></li>
<?php endif; ?>

<?php if ($this->next && is_array($this->next)) : ?>
  <li><a href="<?php echo $this->urlHelper->getPostView($this->next); ?>">下一篇：<small><?php echo $this->next['title']; ?></small></a></li>
<?php endif; ?>
</ul>

<?php
$this->widget(
	'views\bootstrap\widgets\PostComments',
	array(
		'html_type' => 'list',
		'post_id' => $this->post_id,
		'comment_status' => $this->comment_status,
	)
);
?>

<?php
$this->widget(
	'views\bootstrap\widgets\PostComments',
	array(
		'html_type' => 'form',
		'post_id' => $this->post_id,
		'comment_status' => $this->comment_status,
	)
);
?>
<p></p>