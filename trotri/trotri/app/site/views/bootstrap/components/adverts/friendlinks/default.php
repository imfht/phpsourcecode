<!-- Friendlinks -->
<?php if ($this->is_show) : ?>
<div class="sidebar-module">
  <h4><?php echo $this->type_name; ?></h4>
  <ol class="list-unstyled">
    <?php foreach ($this->rows as $i => $row) : ?>
    <li><?php echo isset($row['show_code']) ? $row['show_code'] : ''; ?></li>
    <?php endforeach; ?>
  </ol>
</div>
<?php endif; ?>
<!-- /Friendlinks -->