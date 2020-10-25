@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">{{$field_info['label']}}字段配置</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        @if ($errors->has('category'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('category') }}</div>
        @endif
        {{ Form::open(array('method' => 'post')) }}
        <div class="checkbox">
            <label>
                <input type="checkbox" name="required" value="1" <?php echo isset($config->required) ? 'checked=""' : ''; ?>>是否必填
            </label>
        </div>
        <div class="form-group">
            <label>设置高度</label>
            <input type="number" name="height" value="{{isset($config->height)?$config->height:''}}" class="form-control max-width-100">
            <p class="help-block">不填或小于等于0均表示未设置,默认为5.</p>
        </div>
        <div class="form-group">
            <label>设置宽度</label>
            <input type="number" name="width" value="{{isset($config->width)?$config->width:''}}" class="form-control max-width-100">
            <p class="help-block">不填或小于等于0均表示未设置,默认无.</p>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="is_editor" value="1" {{isset($config->is_editor)?'checked=""':''}}>开启百度编辑器
            </label>
        </div>
        <input type="hidden" name="type" value="textarea">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

