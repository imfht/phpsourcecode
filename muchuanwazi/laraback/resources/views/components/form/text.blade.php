<div class="form-group">
{{ Form::label($name, $label, ['class' => 'control-label']) }}
{{ Form::text($name, $value?$value:old($name), array_merge(['class' => 'form-control','id'=>$name], $attributes)) }}
</div>