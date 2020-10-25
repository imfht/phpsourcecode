<?php
/*
 * textarea文本域
 */
?>

@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <textarea name="{{$field['field_name']}}"  id="ueditor">{{$field['value']}}</textarea>
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <textarea name="{{$field['field_name']}}"  id="ueditor"></textarea>
</div>
@endif