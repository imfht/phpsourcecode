<!-- FormBuilder -->
<?php $html = $this->getHtml(); ?>
<?php echo $this->form_open; ?>

<ul class="nav nav-tabs">
<?php foreach ($this->tabs as $tid => $tab) : ?>
<?php $tag = $html->a($tab['prompt'], '#' . $tid, array('data-toggle' => 'tab')); ?>
<?php echo $html->tag('li', ($tab['active'] ? array('class' => 'active') : array()), $tag), "\n"; ?>
<?php endforeach; ?>
</ul><!-- /.nav nav-tabs -->

<div class="form-group">
  <div class="col-lg-1"></div>
  <div class="col-lg-11"><?php echo $this->form_buttons; ?></div>
</div><!-- /.form-group -->

<div class="tab-content">

<?php foreach ($this->tabs as $tid => $tab) : ?>
<?php $attributes = array('id' => $tid, 'class' => 'tab-pane fade' . ($tab['active'] ? ' active in' : '')); ?>
<?php echo $html->openTag('div', $attributes), "\n"; ?>
<?php echo $this->form_inputs[$tid]?>
<?php echo "\n", $html->closeTag('div'); ?>
<?php endforeach; ?>

</div><!-- /.tab-content -->

<div class="form-group">
  <div class="col-lg-1"></div>
  <div class="col-lg-11"><?php echo $this->form_buttons; ?></div>
</div><!-- /.form-group -->

<?php echo $this->form_close; ?>
<!-- /FormBuilder -->
