<!-- Fourpics - <?php echo $this->title; ?> -->
<?php if ($this->is_show && count($this->rows) >= 4) : ?>

<?php $urlHelper = $this->getView()->urlHelper; ?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">
      <?php if ($this->url !== '') : ?>
      <a href="<?php echo $this->url; ?>"><?php echo $this->title; ?>&nbsp;&gt;&gt;</a>
      <?php else : ?>
      <?php echo $this->title; ?>
      <?php endif; ?>
    </h3>
  </div>
  <div class="panel-body">

<?php $rows = $this->rows; $row = array_shift($rows); ?>
<?php if (is_array($row)) : ?>
    <div class="blog-post">
      <div class="blog-post-picture">
        <a href="<?php echo $urlHelper->getPostView($row); ?>" target="_blank">
          <img width="200px" height="150px" src="<?php echo $row['picture']; ?>" alt="<?php echo $row['title']; ?>" />
        </a>
      </div>
      <div class="blog-post-description">
        <h2><a href="<?php echo $urlHelper->getPostView($row); ?>"><?php echo $row['title']; ?></a></h2>
        <p class="blog-post-meta"><?php echo $row['dt_created']; ?> by <?php echo $row['creator_name']; ?></p>
        <p><?php echo $row['description']; ?></p>
      </div>
    </div><!-- /.blog-post -->
<?php endif; ?>

    <div class="blog-post">
      <div class="row">
<?php foreach ($rows as $row) : ?>
<?php if (is_array($row)) : ?>
        <div class="col-xs-6 col-lg-4">
          <a href="<?php echo $urlHelper->getPostView($row); ?>" target="_blank">
            <img width="200px" height="150px" src="<?php echo $row['picture']; ?>" alt="<?php echo $row['title']; ?>">
          </a>
          <a class="blog-post-pic-title" href="<?php echo $urlHelper->getPostView($row); ?>"><?php echo $row['title']; ?></a>
          <p><?php echo $row['description']; ?></p>
        </div>
<?php endif; ?>
<?php endforeach; ?>
      </div>
    </div><!-- /.blog-post -->

  </div>
</div>
<?php endif; ?>
<!-- /Fourpics -->
