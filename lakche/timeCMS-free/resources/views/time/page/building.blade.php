@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    @include($theme.'.user.left')
                </div>
                <div class="col-sm-10" id="message">
                    <div class="panel panel-primary">
                        <div class="panel-heading">系统提示</div>
                        <div class="panel-body">
                            <div class="alert alert-danger text-center" role="alert">该功能正在开发中...</div>
                            <div class="text-center">
                                    <a href="/" class="btn btn-default }}">返回首页</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection