@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">用户信息</h3>
                </div>


                {!! Form::model($user, ['route' => ['user.store_my']]) !!}

                <div class="box-body">
                    @include('errors.submitError')
                    {{ Form::bsText('name','用户名','',['readonly']) }}
                    {{ Form::bsEmail('email','电子邮件') }}
                    {{ Form::bsPassword('password','密码') }}
                    {{ Form::bsPassword('password_confirmation','确认密码') }}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ Form::bsButton('cancel','btn-default','取消',url('admin')) }}
                    {{ Form::bsButton('submit','btn-info pull-right','保存') }}
                </div><!-- /.box-footer-->
                {!! Form::close() !!}
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection