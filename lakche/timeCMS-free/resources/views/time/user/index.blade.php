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
                        <div class="panel-heading">欢迎使用{{ config('system.title') }}</div>
                        <div class="panel-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection