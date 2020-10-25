<!-- Knowmysite -->
<?php if ($this->is_show) : ?>
<div class="sidebar-module sidebar-module-inset" id="poll_<?php echo $this->poll_key; ?>">
  <h4>
    <?php echo $this->poll_name; ?>
    <?php if ($this->is_multiple && $this->max_choices > 0) : ?>
      <small><?php echo sprintf($this->getView()->CFG_SYSTEM_GLOBAL_POLLS_MAX_CHOICES, $this->max_choices); ?></small>
    <?php endif; ?>
  </h4>
  <ol class="list-unstyled">
    <?php foreach ($this->options as $i => $row) : ?>
    <li>
      <?php if ($this->is_multiple) : ?>
        <?php echo $this->getHtml()->checkbox($this->poll_key . '[]', $row['option_id']); ?>
      <?php else : ?>
        <?php echo $this->getHtml()->radio($this->poll_key, $row['option_id']); ?>
      <?php endif; ?>

      <?php echo isset($row['option_name']) ? $row['option_name'] : ''; ?>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php echo $this->getHtml()->button($this->getView()->CFG_SYSTEM_GLOBAL_SUBMIT, '', array('onclick' => 'return Core.vote(\'' . $this->poll_key . '\', \'' . ($this->is_multiple ? 'checkbox' : 'radio') . '\');')); ?>
</div>
<?php endif; ?>
<!-- /Knowmysite -->