<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
  <head>
<?php
if ($this->use_header) :
	$this->display('header');
else :
?>

<!-- Header -->
<?php echo $this->getHtml()->contentType(); ?>
<?php echo $this->getHtml()->meta('IE=edge', 'X-UA-Compatible', ''); ?>
<?php echo $this->getHtml()->meta('width=device-width, initial-scale=1.0', '', 'viewport'); ?>
<?php echo $this->getHtml()->meta($this->meta_keywords, '', 'keywords'); ?>
<?php echo $this->getHtml()->meta($this->meta_description, '', 'description'); ?>
<?php echo $this->getHtml()->meta('', '', 'author'); ?>
<title><?php echo $this->meta_title; ?></title>
<script type="text/javascript">
var g_url = "<?php echo $this->script_url; ?>"; var g_uri = "<?php echo $this->request_uri; ?>"; var g_logId = "<?php echo $this->log_id; ?>";
var g_mod = "<?php echo $this->module; ?>"; var g_ctrl = "<?php echo $this->controller; ?>"; var g_act = "<?php echo $this->action; ?>";
</script>
<!-- /Header -->

<?php endif; ?>

<?php echo $this->html_head; ?>
<?php echo $this->getHtml()->css($this->html_style); ?>
<?php echo $this->getHtml()->js($this->html_script); ?>
  </head>

  <body>
<?php
if ($this->use_header) {
	$this->widget('components\menus\NavBar');
}
?>

<?php echo $this->html_body; ?>

<?php
if ($this->use_footer) {
	$this->display('footer');
	$this->display('scripts');
}
?>
  </body>
</html>