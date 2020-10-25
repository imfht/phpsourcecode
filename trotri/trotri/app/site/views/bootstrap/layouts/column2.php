<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php $this->display('header'); ?>
  </head>

  <body>
<?php $this->widget('components\menus\NavBar'); ?>

<div class="container">

  <!-- Main-Header -->
  <div class="blog-header">
<?php if (isset($this->beforeLayoutContent)) { echo $this->beforeLayoutContent; } ?>
  </div>
  <!-- /Main-Header -->

  <div class="row">

    <!-- Main-Content -->
    <div class="col-sm-9 blog-main">
<?php echo $this->layoutContent; ?>
    </div><!-- /.blog-main -->
    <!-- /Main-Content -->

    <!-- Main-SideBar -->
    <div class="col-sm-3 blog-sidebar">
<?php $this->display($this->sidebar); ?>
    </div>
    <!-- /Main-SideBar -->

  </div>

</div><!-- /.container -->

<?php $this->display('footer'); ?>
<?php $this->display('scripts'); ?>

  </body>
</html>