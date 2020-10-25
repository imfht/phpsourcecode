@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">编辑字段</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        {{ Form::open(array('method' => 'post')) }}
        @if($errors->all())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </div>
        @endif
        <div class="form-group">
            <label>标签</label>
            <input name="label" class="form-control" value="{{Input::old('label')?Input::old('label'):$current_field['label']}}" maxlength="32" required="" >
            <p class="help-block">标签：用于显示给用户的名字</p>
        </div>
        <div class="form-group">
            <label>字段名字</label>
            <input name="field_name" class="form-control" value="{{Input::old('field_name')?Input::old('field_name'):$current_field['field_name']}}" maxlength="64" disabled="" required="">
        </div>
        <div class="form-group">
            <label>字段类型</label>
            <input name="field_type" type="text" disabled="" value="{{Input::old('field_type')?Input::old('field_type'):$current_field['name']}}" class="form-control" maxlength="64" required="" disabled="">
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>
@stop