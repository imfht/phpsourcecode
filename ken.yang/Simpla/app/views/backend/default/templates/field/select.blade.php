<?php
/*
 * text文本框
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <select class="form-control" name="{{$field['field_name']}}">
        @foreach($field['config_data'] as $key=>$value)
        <option value="{{$value}}" <?php if ($value == $field['value']) {echo 'selected';} ?>>{{$value}}</option>
        @endforeach
    </select>
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <select class="form-control" name="{{$field['field_name']}}">
        @foreach($field['config_data'] as $key=>$value)
        <option value="{{$value}}">{{$value}}</option>
        @endforeach
    </select>
</div>
@endif
