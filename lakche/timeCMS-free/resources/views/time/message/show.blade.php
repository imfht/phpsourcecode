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
                            <div class="alert alert-danger text-center" role="alert">{{ $message }}</div>
                            <div class="text-center">
                                @foreach($url as $key => $value)
                                    <a href="{{ $value['url'] }}" class="btn btn-{{ $value['style'] or 'default' }}">{{ $key }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection