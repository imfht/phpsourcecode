<?php
/*
 * 分类
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <select class="form-control" name="{{$field['field_name']}}">
        {{Base::outputOptionTree($field['category_list'],$field['value'])}}
    </select>
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <select class="form-control" name="{{$field['field_name']}}">
        {{Base::outputOptionTree($field['category_list'])}}
    </select>
</div>
@endif