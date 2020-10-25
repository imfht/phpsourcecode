<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php $this->display('header'); ?>
  </head>

  <body>
<?php $this->widget('components\menus\NavBar'); ?>

<div class="container">
  <div class="blog-header"></div>

  <div class="row">

<?php echo $this->layoutContent; ?>

  </div><!-- /.row -->
</div><!-- /.container -->

<?php $this->display('footer'); ?>
<?php $this->display('scripts'); ?>

  </body>
</html>