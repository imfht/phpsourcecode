@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">编辑角色</h3>
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
            <label>角色名<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="title" class="form-control" value="{{Input::old('title')?Input::old('title'):$role['title']}}" maxlength="64" required="">
        </div>
        <div class="form-group">
            <label>描述</label>
            <textarea name="description" class="form-control" maxlength="256">{{Input::old('description')?Input::old('description'):$role['description']}}</textarea>
        </div>
        <button class="btn btn-default" type="submit">保存</button>
        {{Form::close()}}
    </div>
</div>
@stop