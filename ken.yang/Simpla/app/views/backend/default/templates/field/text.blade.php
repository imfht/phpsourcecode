<?php
/*
 * text文本框
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <input type="text" class="form-control" name="{{$field['field_name']}}" value="{{$field['value']}}">
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <input type="text" class="form-control" name="{{$field['field_name']}}" value="">
</div>
@endif
