<!-- Footer -->
<hr>

<?php $this->display('components/dialog/trash_remove'); ?>
<?php $this->display('components/dialog/alert'); ?>
<?php $this->display('components/dialog/ajax_view'); ?>

<footer>
<!-- p>&copy; Company 2013</p -->
</footer>

<?php if ($this->warning_backtrace) : ?>
<div class="alert alert-danger"><?php echo $this->warning_backtrace; ?></div>
<?php endif; ?>

<!-- /Footer -->
