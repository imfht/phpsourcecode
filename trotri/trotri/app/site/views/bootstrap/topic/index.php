<?php if (isset($this->rows)) : ?>
<?php foreach ($this->rows as $row) : ?>
<div class="row featurette">
  <a href="<?php echo $this->urlHelper->getTopicView($row); ?>" target="_blank">
    <img class="featurette-image img-responsive" src="<?php echo $row['cover']; ?>" alt="<?php echo $row['topic_name']; ?>" title="<?php echo $row['topic_name']; ?>">
  </a>
</div>
<hr/>
<?php endforeach; ?>
<?php endif; ?>

<?php
$this->widget(
	'views\bootstrap\widgets\PaginatorBuilder',
	array(
		'url' => $this->getUrlManager()->getUrl('index', 'show', 'topic'),
		'total' => $this->total,
		'limit' => $this->limit,
		'offset' => $this->offset,
	)
);
?>