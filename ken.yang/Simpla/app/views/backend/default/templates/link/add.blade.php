@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">添加友情连接</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        @if($errors->all())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </div>
        @endif
        {{ Form::open(array('url' => '/admin/link/add','method' => 'post','enctype'=>'multipart/form-data')) }}
        <div class="form-group">
            <label>标题<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="title" class="form-control" maxlength="32" required="" value='{{Input::old('title')}}'>
        </div>
        <div class="form-group">
            <label>连接<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="url" class="form-control" maxlength="256" required="" value='{{Input::old('url')}}'>
        </div>
        <div class="form-group">
            <label>描述</label>
            <input type="text" name="description" class="form-control" maxlength="128" value='{{Input::old('description')}}'>
        </div>
        <div class="form-group">
            <label>图片</label>
            <input type="file" name="image">
        </div>
        <div class="form-group">
            <label>位置</label>
            <input type="number" name="weight" class="form-control" maxlength="10" required="" value='{{Input::old('weight')?Input::old('weight'):'0'}}'>
        </div>
        <input class="btn btn-primary" type="submit" value="添加"/>
        {{ Form::close() }}


    </div>
</div>
<!-- /.row -->
@stop