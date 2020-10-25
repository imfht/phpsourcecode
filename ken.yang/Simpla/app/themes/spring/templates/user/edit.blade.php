<?php
/**
 * 方法：
 * --$errors->all()：获取所有错误信息
 * --Request::query('back_url')：获取来源地址，修改后进行返回
 */
?>
@extends('Theme::layout.page')

@section('content')
<div class="page-header">
    <h3>编辑用户个人信息</h3>
</div>
{{ Form::open(array('method' => 'post','enctype'=>'multipart/form-data')) }}


@if($errors->all())
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    @foreach($errors->all() as $error)
    {{$error}}<br/>
    @endforeach
</div>
@endif
<div class="form-group">
    <label for="exampleInputFile">修改头像</label>
    <input type="file" id="picture" name='picture'>
    <small class="help-block">只允许上传大小不超过2M的JPG,PNG格式的图片！</small>
</div>
<div class="form-group">
    <label for="password">修改密码：</label>
    <input type="password" class="form-control" name="password" placeholder="新密码" maxlength="20">
</div>
<div class="form-group">
    <input type="password" class="form-control" name="password_confirmation" placeholder="请再次输入新密码" maxlength="20">
    <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_），最小6个字符，最大20个字符</small>
</div>
<input type="hidden" name="back_url" value="{{Request::query('back_url')}}">
<button type="submit" class="btn btn-primary">保存</button>
{{ Form::close() }}

@stop