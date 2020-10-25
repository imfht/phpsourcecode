<div class="form-group">
{{ Form::label($label, null, ['class' => 'control-label']) }}
{{ Form::textarea($name, $value?$value:old($name), array_merge(['class' => 'form-control'], $attributes)) }}
</div>