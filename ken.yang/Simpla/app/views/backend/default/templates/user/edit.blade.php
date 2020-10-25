@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">编辑用户</h3>
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
        {{ Form::open(array('method' => 'post','class' => 'form-signin')) }}
        <div class="form-group">
            <label>用户名<span class="text-red" title="此项必填">*</span></label>
            <input type="text" name="username" class="form-control" value="{{Input::old('username')?Input::old('username'):$user['username']}}" maxlength="32" required="">
            <small class="help-block">可以使用英文或者中文及其他字符，最小6个字符，最大32个字符</small>
        </div>
        <div class="form-group">
            <label>邮箱<span class="text-red" title="此项必填">*</span></label>
            <input type="email" name="email" class="form-control" value="{{Input::old('email')?Input::old('email'):$user['email']}}" maxlength="256" required="">
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="text" placeholder="请输入密码" name="password" class="form-control" maxlength="20">
        </div>
        <div class="form-group">
            <input type="text" placeholder="请再次输入密码" name="password_confirmation" class="form-control" maxlength="20">
            <small class="help-block">仅允许字母、数字、破折号（-）以及底线（_）,最小6个字符，最大20个字符</small>
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="radio">
                <label>
                    <input type="radio" <?php echo $user['status'] ? 'checked=""' : '' ?> value="1" name="status">活跃
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" <?php echo !$user['status'] ? 'checked=""' : '' ?> value="0" name="status">锁定
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>选择角色</label>
            @foreach($roles as $role)
            <div class="radio">
                <label>
                    <input type="radio" <?php echo ($user_role == $role['id']) ? 'checked=""' : '' ?> value="{{$role['id']}}" id="optionsRadios1" name="roles">{{$role['title']}}
                </label>
            </div>
            @endforeach
        </div>
        <input class="btn btn-primary" type="submit" value="保存"/>
        {{ Form::close() }}
    </div>
</div>
<!-- /.row -->


@stop