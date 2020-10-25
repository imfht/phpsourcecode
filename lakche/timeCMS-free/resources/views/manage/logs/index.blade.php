@extends($theme.'.layouts.app')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset($theme.'/css/admin.css') }}"/>
    <script type="text/javascript" src="{{ asset($theme.'/js/admin.js') }}"></script>
    <input type="hidden" name="_token" id="TOKEN" value="{{ csrf_token() }}"/>
    <div class="container-fluid" id="main">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    @include($theme.'.left')
                </div>
                <div class="col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            系统日志
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>用户ID</th>
                                    <th>模块</th>
                                    <th>模块对象</th>
                                    <th>操作</th>
                                    <th>简介</th>
                                    <th>IP</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->user_id }}</td>
                                        <td>{{ $log->module }}</td>
                                        <td>{{ $log->module_id }}</td>
                                        <td>{{ $log->operation }}</td>
                                        <td>{{ $log->info }}</td>
                                        <td>{{ $log->ip }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <div class="pagination" style="text-align:center;">{!! $logs->render() !!}</div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection