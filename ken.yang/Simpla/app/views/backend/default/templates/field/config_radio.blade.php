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
        @if ($errors->has('value'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('value') }}</div>
        @endif
        {{ Form::open(array('method' => 'post')) }}
        <div class="checkbox">
            <label>
                <input type="checkbox" name="required" value="1" <?php echo isset($config->required) ? 'checked=""' : ''; ?>>是否必填
            </label>
        </div>
        <div class="form-group">
            <label>添加值</label>
            <textarea name="value" rows="5" class="form-control">{{isset($config->value)?$config->value:''}}</textarea>
            <p class="help-block">请务必使用key|value的形式，每行一个.</p>
        </div>
        <input type="hidden" name="type" value="radio">
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>


@stop

