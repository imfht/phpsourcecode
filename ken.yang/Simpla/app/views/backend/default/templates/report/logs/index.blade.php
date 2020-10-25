@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">用户操作日志</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-warning" role="alert">
            这里记录了用户的日常操作，如果你需要更加详细的系统日志，请查看app/storage/logs文件夹下面的日志文件。
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>日志ID</th>
                        <th>消息</th>
                        <th>类型</th>
                        <th>用户</th>
                        <th>时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{$log['id']}}</td>
                        <td>{{$log['message']}}</td>
                        <td>{{$log['type']}}</td>
                        <td>{{$log->user['username']}}</td>
                        <td>{{$log['created_at']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$logs->links()}}
        </div>
    </div>
</div>


@stop

