<?php
/*
 * image图像上传
 */
?>
@if(isset($field['value']))
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php $values = explode(',', $field['value']) ?>
            @foreach($values as $row)
            <div class="node-edit-img">
                <img src="/{{$row}}" width="100px" height="100%"/>
                <p><small class="img_delete" name="{{$row}}">删除</small></p>
            </div>
            @endforeach
        </div>
        <input type="hidden" name="{{$field['field_name'].'_delete'}}" id="{{$field['field_name'].'_delete'}}" value="">
    </div>
    @for($i = 0 ;$i<$field['config_data']['file_max_num'] - count($values);$i++)
    <p><input type="file" name="{{$field['field_name'].'_'.$i}}"></p>
    @endfor
</div>
@else

@if($field['config_data']['file_max_num'] == 0)
<div class="form-group">
    <label>{{$field['label']}}</label>
    <div class="image_upload"><input type="file" name="{{$field['field_name'].'_0'}}"><br/></div>
    <button type="button" class="btn btn-default btn-xs add_more" value="0">点击继续添加</button>
</div>
@else

<div class="form-group">
    <label>{{$field['label']}}</label>
    @for($i = 0 ;$i<$field['config_data']['file_max_num'];$i++)
    <p><input type="file" name="{{$field['field_name'].'_'.$i}}"></p>
    @endfor
</div>

@endif
@endif

<script type="text/javascript">
    $(document).ready(function () {
        var field_name = "{{$field['field_name']}}";
        //添加更多图片
        $(".add_more").click(function () {
            var value = parseInt($(this).attr('value')) + 1;
            var html = '<input type="file" name="' + field_name + '_' + value + '"><br/>';
            $(".image_upload").append(html);
            $(".add_more").attr('value', value);
        });
        //删除图片
        var del_name = field_name + '_delete';
        $("." + del_name).click(function () {
            var name = $(this).attr('name');
            var old_value = $("#" + del_name).val();
            if (old_value) {
                $("#" + del_name).attr('value', old_value + ',' + name);
            } else {
                $("#" + del_name).attr('value', name);
            }
            $(this).parent().parent().remove();
        });
    });
</script>
