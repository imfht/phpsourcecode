<!-- JavaScript -->
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/bootstrap/' . $this->skinVersion . '/js/bootstrap.min.js'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/bootstrap-switch/bootstrap-switch.js'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/jquery-icheck/icheck.min.js'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js'); ?>
<!-- /JavaScript -->

<!-- Trotri JS -->
<?php echo $this->getHtml()->jsFile($this->static_url . '/js/trotri-1.0.0.js'); ?>
<!-- Custom JS -->
<?php echo $this->getHtml()->jsFile($this->js_url . '/template.js?v=' . $this->version); ?>
