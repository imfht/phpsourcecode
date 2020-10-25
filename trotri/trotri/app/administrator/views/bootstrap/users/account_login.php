<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php $this->display('header'); ?>
<?php echo $this->getHtml()->cssFile($this->css_url . '/login.css?v=' . $this->version); ?>
  </head>

  <body>

<?php $this->widget('views\bootstrap\components\bar\AlertBar'); ?>

<div class="container">

<?php echo $this->getHtml()->openForm($this->getUrlManager()->getUrl($this->action), 'post', array('class' => 'form-signin')); ?>
  <h2 class="form-signin-heading"><?php echo $this->CFG_SYSTEM_URLS_ADMINISTRATOR; ?></h2>
  <?php echo $this->getHtml()->text('login_name', $this->data['login_name'], array('class' => 'form-control', 'placeholder' => $this->CFG_SYSTEM_GLOBAL_LOGIN_NAME, 'required', 'autofocus')); ?>
  <?php echo $this->getHtml()->password('password', '', array('class' => 'form-control', 'placeholder' => $this->CFG_SYSTEM_GLOBAL_LOGIN_PASSWORD, 'required')); ?>
  <div class="checkbox"><label>
    <?php echo $this->getHtml()->checkbox('remember_me', 1, $this->data['remember_me']); ?> <?php echo $this->CFG_SYSTEM_GLOBAL_REMEMBER_ME; ?>
  </label></div>
  <?php echo $this->getHtml()->hidden('http_referer', $this->http_referer); ?>
  <?php echo $this->getHtml()->submit($this->CFG_SYSTEM_GLOBAL_LOGIN, '', array('class' => 'btn btn-lg btn-primary btn-block')); ?>
<?php echo $this->getHtml()->closeForm(); ?>

</div><!-- /.container -->

<?php echo $this->getHtml()->jsFile($this->js_url . '/mods/users.js?v=' . $this->version); ?>
<?php $this->display('scripts'); ?>

  </body>
</html>