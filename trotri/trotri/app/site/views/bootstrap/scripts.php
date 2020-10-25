<!-- JavaScript -->
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/bootstrap/' . $this->skinVersion . '/js/bootstrap.min.js'); ?>
<!-- /JavaScript -->

<!-- Trotri JS -->
<?php echo $this->getHtml()->jsFile($this->static_url . '/js/trotri-1.0.0.js'); ?>
<!-- Custom JS -->
<?php echo $this->getHtml()->jsFile($this->js_url . '/template.js?v=' . $this->version); ?>

<?php echo $this->stat_code; ?>
