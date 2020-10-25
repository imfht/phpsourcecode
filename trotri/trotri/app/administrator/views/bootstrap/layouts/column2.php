<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php $this->display('header'); ?>
  </head>

  <body>

<?php $this->widget('views\bootstrap\components\bar\NavBar'); ?>

<?php $this->widget('views\bootstrap\components\bar\AlertBar'); ?>

<div class="container">

  <div class="row row-offcanvas row-offcanvas-right">

<?php $this->display($this->sidebar); ?>

<!-- Right -->
<div class="col-xs-12 col-sm-10">
  <div class="row">

<?php echo $this->layoutContent; ?>

  </div><!-- /.row -->
</div><!-- /.col-xs-12 col-sm-10 -->
<!-- /Right -->

  </div><!-- /.row -->

<?php $this->display('footer'); ?>

</div><!-- /.container -->

<?php $this->display('scripts'); ?>

  </body>
</html>