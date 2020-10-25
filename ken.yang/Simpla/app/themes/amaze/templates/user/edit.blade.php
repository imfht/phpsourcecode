<?php
/**
 * 方法：
 * --$errors->all()：获取所有错误信息
 * --Request::query('back_url')：获取来源地址，修改后进行返回
 */
?>
@extends('Theme::layout.page')

@section('content')

<div class="am-g">
    <div class="am-u-lg-12 am-u-md-12 am-u-sm-centered">
        <p></p>
        <h3>编辑用户个人信息</h3>
        <hr>
        @if($errors->all())
        <p class="am-text-danger">
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </p>
        @endif
        {{ Form::open(array('method' => 'post','enctype'=>'multipart/form-data','class' => 'am-form')) }}
        <label for="email">修改头像:</label>
        <div class="am-form-group am-form-file">
        <button type="button" class="am-btn am-btn-danger am-btn-sm">
            <i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
        <input type="file" id="picture" name='picture' multiple>
        </div>
        <div id="file-list"></div>
        <br>
        <label for="password">修改密码:</label>
        <input type="password" class="form-control" name="password" placeholder="新密码" maxlength="20">
        <input type="password" class="form-control" name="password_confirmation" placeholder="请再次输入新密码" maxlength="20">
        <br>

        <div class="am-cf">
            <input type="hidden" name="back_url" value="{{Request::query('back_url')}}">
            <input type="submit" name="" value="保 存" class="am-btn am-btn-primary am-btn-sm am-fl btn-loading">
        </div>
        {{ Form::close() }}
        <br>
    </div>
</div>


@stop