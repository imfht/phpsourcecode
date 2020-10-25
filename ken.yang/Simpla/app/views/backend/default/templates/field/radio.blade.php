<?php
/*
 * text文本框
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="radio">
        @foreach($field['config_data'] as $key=>$value)
        <label>
            <input type="radio" name="{{$field['field_name']}}" value="{{$value}}"  <?php if($value == $field['value']){echo 'checked';}?>>{{$value}}
        </label>
        @endforeach
    </div>
</div>
@else
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="radio">
        @foreach($field['config_data'] as $key=>$value)
        <label>
            <input type="radio" name="{{$field['field_name']}}" value="{{$value}}">{{$value}}
        </label>
        @endforeach
    </div>
</div>
@endif
