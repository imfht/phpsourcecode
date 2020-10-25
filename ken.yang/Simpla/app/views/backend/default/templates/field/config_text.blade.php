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
            <label>允许最小长度</label>
            <input type="number" name="min_len" value="{{isset($config->min_len)?$config->min_len:''}}" class="form-control max-width-100">
            <p class="help-block">不填或小于等于0均表示无任何限制.</p>
        </div>
        <div class="form-group">
            <label>允许最大长度</label>
            <input type="number" name="max_len" value="{{isset($config->max_len)?$config->max_len:''}}" class="form-control max-width-100">
            <p class="help-block">不填或小于等于0均表示无任何限制.</p>
        </div>
        <input type="hidden" name="type" value="text">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

