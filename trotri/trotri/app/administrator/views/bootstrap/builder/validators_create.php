<?php
$this->widget('views\bootstrap\widgets\FormBuilder',
	array(
		'name' => 'create',
		'action' => $this->getUrlManager()->getUrl($this->action),
		'errors' => $this->errors,
		'elements_object' => $this->elements,
		'elements' => array(
			'field_id' => array(
				'value' => $this->field_id
			),
			'field_name' => array(
				'value' => $this->elements->getHtmlLabelByFieldId($this->field_id),
			)
		),
		'columns' => array(
			'validator_name',
			'field_name',
			'options',
			'option_category',
			'message',
			'sort',
			'when',
			'field_id',
			'_button_save_',
			'_button_saveclose_',
			'_button_savenew_',
			'_button_cancel_'
		)
	)
);
?>

<script type="text/javascript">
var messageEnum = <?php echo $this->message_enum; ?>;
var optionCategoryEnum = <?php echo $this->option_category_enum; ?>;
</script>