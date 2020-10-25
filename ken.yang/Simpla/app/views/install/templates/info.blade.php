@extends('InstallTheme::layout.page')

@section('content')
<div class="listing listing-success">
    <div class="shape">
        <div class="shape-text">{{$version}}</div>
    </div>
    <div class="listing-content">
        <h3 class="lead">Simpla安装向导<small>第三步</small></h3>
        <hr>
        {{ Form::open(array('method' => 'post')) }}
        <!--错误提示-->
        @if($errors->all())
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br/>
            @endforeach
        </div>
        @endif
        <!--数据库连接配置-->
        <div class="panel panel-default">
            <div class="panel-heading">数据库配置</div>
            <div class="panel-body">
                <p class="text-warning">默认使用MySQL数据库，如果你想使用其他数据库，请编辑app/config/database.php文件。</p>

                <!--数据库信息-->
                <div class="form-group">
                    <label>数据库主机<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="db_hostname" class="form-control" maxlength="100" required="" value='{{Input::old('db_prefix')?Input::old('db_prefix'):'localhost'}}'>
                </div>
                <div class="form-group">
                    <label>数据库名字<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="db_name" class="form-control" maxlength="20" required="" value="{{Input::old('db_name')}}">
                </div>
                <div class="form-group">
                    <label>数据库账户<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="db_username" class="form-control" maxlength="30" required="" value="{{Input::old('db_username')}}">
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="text" name="db_password" class="form-control" maxlength="30" value="{{Input::old('db_password')}}">
                </div>
                <div class="form-group">
                    <label>数据库前缀<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="db_prefix" class="form-control" maxlength="20" required="" value="{{Input::old('db_prefix')?Input::old('db_prefix'):'sp_'}}">
                </div>
            </div>
        </div>

        <!--账户信息配置-->
        <div class="panel panel-default">
            <div class="panel-heading">管理员配置</div>
            <div class="panel-body">

                <div class="form-group">
                    <label>管理员用户名<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="username" class="form-control" maxlength="20" required="" value='{{Input::old('username')}}'>
                </div>
                <div class="form-group">
                    <label>管理员密码<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="password" class="form-control" maxlength="20" required="">
                    <small class="help-block">密码仅允许字母、数字、破折号（-）以及底线（_）</small>
                </div>
                <div class="form-group">
                    <label>再次输入管理员密码<span class="text-red" title="此项必填">*</span></label>
                    <input type="text" name="password_confirmation" class="form-control" maxlength="20" required="">
                </div>
                <div class="form-group">
                    <label>管理员邮箱<span class="text-red" title="此项必填">*</span></label>
                    <input name="email" type="email" class="form-control" maxlength="64" value='{{Input::old('email')}}'>
                </div>
                <hr>
                <p class="text-align-center">
                    <button class="btn btn-primary" type="submit">进行安装</button>
                </p>
            </div>
        </div>
        {{Form::close()}}
    </div>
</div>

@stop