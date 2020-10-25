<!-- FormPostComments -->
<?php $html = $this->getHtml(); ?>
<?php echo $html->openTag('div', array('id' => 'comm_response')); ?>
<?php echo $html->openTag('h3'); ?>
<?php echo $this->title; ?>
<?php echo $html->tag('span', array('class' => 'comm-response-explain'), $this->hint); ?>
<?php echo $html->tag('span', array('class' => 'glyphicon glyphicon-remove comm-response-remove-reply'), ''); ?>
<?php echo $html->closeTag('h3'); ?>

<?php echo $this->form_open; ?>

<?php echo $this->form_inputs; ?>

<?php echo $this->form_buttons; ?>
<?php echo $this->form_close; ?>
<?php echo $html->closeTag('div'); ?>
<!-- /FormPostComments -->
