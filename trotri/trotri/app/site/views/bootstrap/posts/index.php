<?php if (isset($this->rows)) : ?>
<?php foreach ($this->rows as $row) : ?>
<div class="blog-post">
  <div class="blog-post-picture">
    <a href="<?php echo $this->urlHelper->getPostView($row); ?>" target="_blank">
      <img width="200px" height="150px" src="<?php echo $row['picture']; ?>" alt="<?php echo $row['title']; ?>" />
    </a>
  </div>
  <div class="blog-post-description">
    <h2><a href="<?php echo $this->urlHelper->getPostView($row); ?>"><?php echo $row['title']; ?></a></h2>
    <p class="blog-post-meta"><?php echo $row['dt_created']; ?> by <?php echo $row['creator_name']; ?></p>
    <p><?php echo $row['description']; ?></p>
  </div>
</div><!-- /.blog-post -->
<hr>
<?php endforeach; ?>
<?php endif; ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	array(
		'url' => $this->url,
		'total' => $this->total,
		'limit' => $this->limit,
		'offset' => $this->offset,
	)
);
?>