@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    @include($theme.'.user.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">修改密码</div>
                        <div class="panel-body">
                            <form id="registerForm" class="loginForm" action="{{ route('user.password.store') }}" method="post">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="password-old">原密码</label>
                                    <input type="password" class="form-control" name="password_old" tabindex="4" placeholder="请输入正在使用的密码">

                                    @if($errors->first('password_old'))
                                        <p class="bg-danger">{{ $errors->first('password_old') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password">新密码</label>
                                    <input type="password" class="form-control" name="password" tabindex="5" placeholder="密码长度为6-20个字符">

                                    @if($errors->first('password'))
                                        <p class="bg-danger">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password">重复密码</label>
                                    <input type="password" class="form-control" name="password_confirmation" tabindex="6" placeholder="请重复密码">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-block" value="重设密码">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection