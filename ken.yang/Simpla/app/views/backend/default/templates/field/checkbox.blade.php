<?php
/*
 * text文本框
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="checkbox">
        @foreach($field['config_data'] as $key=>$value)
        <label>
            <input type="checkbox" name="{{$field['field_name']}}[{{$key}}]" value="{{$value}}"  <?php if(in_array($value, $field['value'])){echo 'checked';}?>>{{$value}}
        </label>
        @endforeach
    </div>
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="checkbox">
        @foreach($field['config_data'] as $key=>$value)
        <label>
            <input type="checkbox" name="{{$field['field_name']}}[{{$key}}]" value="{{$value}}">{{$value}}
        </label>
        @endforeach
    </div>
</div>
@endif
