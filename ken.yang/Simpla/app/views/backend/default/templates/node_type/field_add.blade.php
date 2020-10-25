@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加字段</h3>
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
            <input name="label" class="form-control"  maxlength="64" required="" value="{{Input::old('label')}}">
            <p class="help-block">标签：用于显示给用户的名字</p>
        </div>
        <div class="form-group">
            <label>字段名字</label>
            <input name="field_name" class="form-control"  maxlength="64" required="" value="{{Input::old('field_name')}}">
            <p class="help-block">即机器名字，用于系统内部设别，请使用英文、数字或者下划线，请勿使用其他字符.</p>
        </div>
        <div class="form-group">
            <label>字段类型</label>
            <select class="form-control" name="field_type">
                @foreach($fields as $field)
                <option value="{{$field['type']}}">{{$field['name']}}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-default" type="submit">添加</button>
        {{Form::close()}}
    </div>
</div>
@stop