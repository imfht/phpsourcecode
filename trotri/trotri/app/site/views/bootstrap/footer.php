<!-- Footer -->
<div class="blog-footer">
  <p><?php echo $this->powerby ; ?>.</p>
  <p>
    <a href="#">Back to top</a>
  </p>
</div>

<?php if ($this->warning_backtrace) : ?>
<div class="alert alert-danger"><?php echo $this->warning_backtrace; ?></div>
<?php endif; ?>
<!-- /Footer -->
