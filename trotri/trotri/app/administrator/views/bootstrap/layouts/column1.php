<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php $this->display('header'); ?>
  </head>

  <body>

<?php $this->widget('views\bootstrap\components\bar\NavBar'); ?>

<div class="container">

<?php echo $this->layoutContent; ?>

</div><!-- /.container -->

<?php $this->display('scripts'); ?>

  </body>
</html>