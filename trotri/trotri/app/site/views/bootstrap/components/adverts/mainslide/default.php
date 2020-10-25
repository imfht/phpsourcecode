<!-- Slideindex -->
<?php if ($this->is_show) : ?>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
  <?php foreach ($this->rows as $i => $row) : ?>
    <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i; ?>" class="<?php echo (($i > 0) ? '' : 'active'); ?>"></li>
  <?php endforeach; ?>
  </ol>
  <div class="carousel-inner">
  <?php foreach ($this->rows as $i => $row) : ?>
    <div class="item <?php echo (($i > 0) ? '' : 'active'); ?>">
      <?php echo isset($row['show_code']) ? $row['show_code'] : ''; ?>
    </div>
  <?php endforeach; ?>
  </div>
  <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<?php endif; ?>
<!-- /Slideindex -->
